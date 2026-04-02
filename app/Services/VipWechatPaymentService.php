<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Services\WechatPay\WechatPayException;
use App\Services\WechatPay\WechatPayV3Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VipWechatPaymentService
{
    public function isConfigured(): bool
    {
        if (! config('wechat_pay.enabled')) {
            return false;
        }

        return $this->requiredConfigPresent();
    }

    public function requiredConfigPresent(): bool
    {
        $c = config('wechat_pay');

        return ! empty($c['app_id'])
            && ! empty($c['mch_id'])
            && ! empty($c['mch_secret_key'])
            && ! empty($c['mch_serial_no'])
            && ! empty($c['notify_url'])
            && is_readable((string) $c['mch_private_key_path']);
    }

    /**
     * @throws WechatPayException
     */
    public function assertReadyForNativePay(): void
    {
        if (! config('wechat_pay.enabled')) {
            throw new WechatPayException('微信支付未启用，请在 .env 设置 WECHAT_PAY_ENABLED=true');
        }
        if (! $this->requiredConfigPresent()) {
            throw new WechatPayException('微信支付参数或商户私钥未配置完整，请检查 .env 与 storage/certs/wechat/apiclient_key.pem');
        }
    }

    /**
     * 创建待支付订单 + 订阅记录，并调用微信 Native 下单。
     *
     * @return array{order: Order, code_url: string}
     */
    public function createNativePurchase(User $user, string $plan): array
    {
        $this->assertReadyForNativePay();

        $plans = config('wechat_pay.plans');
        if (! isset($plans[$plan])) {
            throw new WechatPayException('无效的会员套餐');
        }

        $label = $plans[$plan]['label'];
        $amountYuan = (float) $plans[$plan]['amount_yuan'];
        if ($amountYuan <= 0) {
            throw new WechatPayException('套餐金额配置无效');
        }

        $totalFen = WechatPayV3Client::yuanToFen($amountYuan);
        $notifyUrl = (string) config('wechat_pay.notify_url');
        $client = WechatPayV3Client::fromConfig();

        return DB::transaction(function () use ($user, $plan, $amountYuan, $label, $totalFen, $notifyUrl, $client) {
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan' => $plan,
                'amount' => $amountYuan,
                'status' => 'pending',
                'started_at' => null,
                'expires_at' => null,
                'payment_id' => null,
                'payment_method' => null,
            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'product_type' => 'subscription',
                'product_id' => $subscription->id,
                'amount' => $amountYuan,
                'status' => 'pending',
                'payment_method' => null,
                'payment_time' => null,
                'paid_amount' => null,
                'remark' => 'wechat_native:' . $plan,
            ]);

            $outTradeNo = $order->order_no;
            $description = config('app.name', 'VIP') . ' - ' . $label;

            $result = $client->nativePay($outTradeNo, $description, $totalFen, $notifyUrl);
            $codeUrl = $result['code_url'];

            Cache::put($this->codeUrlCacheKey($outTradeNo), $codeUrl, now()->addHours(2));

            return ['order' => $order->fresh(), 'code_url' => $codeUrl];
        });
    }

    public function getCachedCodeUrl(string $orderNo): ?string
    {
        return Cache::get($this->codeUrlCacheKey($orderNo));
    }

    /**
     * 处理微信支付异步通知（已验签、已解密 resource）
     *
     * @param  array<string, mixed>  $data
     */
    public function fulfillIfPaid(array $data): void
    {
        $outTradeNo = $data['out_trade_no'] ?? '';
        $tradeState = $data['trade_state'] ?? '';
        $transactionId = $data['transaction_id'] ?? '';
        $amountTotal = (int) ($data['amount']['total'] ?? 0);

        if ($outTradeNo === '' || $tradeState !== 'SUCCESS') {
            return;
        }

        DB::transaction(function () use ($outTradeNo, $transactionId, $amountTotal) {
            /** @var Order|null $order */
            $order = Order::query()->where('order_no', $outTradeNo)->lockForUpdate()->first();
            if (! $order || $order->status === 'paid') {
                return;
            }

            $expectedFen = WechatPayV3Client::yuanToFen((float) $order->amount);
            if ($amountTotal !== $expectedFen) {
                Log::warning('wechat_notify_amount_mismatch', [
                    'order_no' => $outTradeNo,
                    'expected_fen' => $expectedFen,
                    'actual_fen' => $amountTotal,
                ]);

                return;
            }

            $subscription = Subscription::query()->where('id', $order->product_id)->lockForUpdate()->first();
            if (! $subscription) {
                Log::error('wechat_notify_subscription_missing', ['order_no' => $outTradeNo]);

                return;
            }

            $user = User::query()->where('id', $order->user_id)->lockForUpdate()->first();
            if (! $user) {
                return;
            }

            $this->grantVipToUser($user, $subscription->plan);

            $endsAt = $user->fresh()->subscription_ends_at;
            $subscription->update([
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => $subscription->plan === 'lifetime' ? null : $endsAt,
                'payment_id' => $transactionId,
                'payment_method' => 'wechat_native',
            ]);

            $order->update([
                'status' => 'paid',
                'payment_method' => 'wechat_native',
                'payment_time' => now(),
                'paid_amount' => $order->amount,
                'wechat_transaction_id' => $transactionId,
            ]);
        });
    }

    public function notifySuccessResponse(): Response
    {
        return response()->json(['code' => 'SUCCESS', 'message' => '成功']);
    }

    public function notifyFailResponse(string $message): Response
    {
        return response()->json(['code' => 'FAIL', 'message' => $message], 400);
    }

    protected function grantVipToUser(User $user, string $plan): void
    {
        $payload = [];

        if ($user->role !== 'admin') {
            $payload['role'] = 'vip';
        }

        if ($plan === 'lifetime') {
            $payload['subscription_ends_at'] = null;
            $user->update($payload);

            return;
        }

        $base = $user->subscription_ends_at && $user->subscription_ends_at->isFuture()
            ? $user->subscription_ends_at->copy()
            : now();

        $payload['subscription_ends_at'] = $plan === 'monthly'
            ? $base->copy()->addMonth()
            : $base->copy()->addYear();

        $user->update($payload);
    }

    protected function codeUrlCacheKey(string $orderNo): string
    {
        return 'wechat_pay:native:code_url:' . $orderNo;
    }
}
