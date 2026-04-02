<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Subscription;
use App\Services\VipWechatPaymentService;
use App\Services\WechatPay\WechatPayException;
use App\Services\WechatPay\WechatPayV3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WechatNativePaymentController extends Controller
{
    public function __construct(
        protected VipWechatPaymentService $vipWechatPaymentService
    ) {
    }

    /**
     * 选择套餐确认页（登录后）
     */
    public function selectPlan(string $plan)
    {
        $plans = config('wechat_pay.plans');
        if (! isset($plans[$plan])) {
            abort(404);
        }

        return view('payments.vip-pay-confirm', [
            'plan' => $plan,
            'planLabel' => $plans[$plan]['label'],
            'amountYuan' => (float) $plans[$plan]['amount_yuan'],
            'wechatReady' => $this->vipWechatPaymentService->isConfigured(),
        ]);
    }

    /**
     * 创建 Native 扫码订单，跳转至展示二维码页
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|string|in:monthly,yearly,lifetime',
        ]);

        try {
            $this->vipWechatPaymentService->assertReadyForNativePay();
            $result = $this->vipWechatPaymentService->createNativePurchase(
                $request->user(),
                $validated['plan']
            );
        } catch (WechatPayException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'order_no' => $result['order']->order_no,
                'redirect_url' => route('payments.wechat.show', ['orderNo' => $result['order']->order_no]),
            ]);
        }

        return redirect()->route('payments.wechat.show', ['orderNo' => $result['order']->order_no]);
    }

    /**
     * 展示支付二维码（code_url 来自缓存，约 2 小时）
     */
    public function show(string $orderNo)
    {
        $order = Order::query()
            ->where('order_no', $orderNo)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status === 'paid') {
            return redirect()->route('vip')->with('success', '支付已完成，感谢支持！');
        }

        $codeUrl = $this->vipWechatPaymentService->getCachedCodeUrl($orderNo);
        if (! $codeUrl) {
            return redirect()
                ->route('vip.pay', ['plan' => $this->guessPlanFromOrder($order)])
                ->with('error', '支付二维码已过期，请重新发起支付。');
        }

        return view('payments.wechat-native', [
            'order' => $order,
            'codeUrl' => $codeUrl,
        ]);
    }

    /**
     * 前端轮询订单是否已支付
     */
    public function status(string $orderNo)
    {
        $order = Order::query()
            ->where('order_no', $orderNo)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'paid' => $order->status === 'paid',
            'redirect_url' => $order->status === 'paid' ? route('vip') : null,
        ]);
    }

    /**
     * 微信异步通知（无 CSRF）
     */
    public function notify(Request $request)
    {
        $body = $request->getContent();
        $timestamp = (string) $request->header('Wechatpay-Timestamp', '');
        $nonce = (string) $request->header('Wechatpay-Nonce', '');
        $signature = (string) $request->header('Wechatpay-Signature', '');

        $payload = json_decode($body, true);
        if (! is_array($payload) || empty($payload['resource'])) {
            return $this->vipWechatPaymentService->notifyFailResponse('invalid body');
        }

        try {
            $client = WechatPayV3Client::fromConfig();
            if (! $client->verifyNotificationSignature($timestamp, $nonce, $body, $signature)) {
                return $this->vipWechatPaymentService->notifyFailResponse('sign verify failed');
            }

            $data = $client->decryptNotifyResource($payload['resource']);
            $this->vipWechatPaymentService->fulfillIfPaid($data);
        } catch (WechatPayException $e) {
            Log::warning('wechat_notify_error', ['msg' => $e->getMessage()]);

            return $this->vipWechatPaymentService->notifyFailResponse($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('wechat_notify_exception', ['exception' => $e]);

            return response()->json(['code' => 'FAIL', 'message' => 'error'], 500);
        }

        return $this->vipWechatPaymentService->notifySuccessResponse();
    }

    protected function guessPlanFromOrder(Order $order): string
    {
        $sub = Subscription::query()->find($order->product_id);

        return $sub?->plan ?? 'yearly';
    }
}
