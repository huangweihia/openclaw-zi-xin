<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * 我的订阅
     */
    public function index()
    {
        $subscriptions = Subscription::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        
        return view('max.subscriptions.index', compact('subscriptions'));
    }

    /**
     * 续费订阅
     */
    public function renew($id)
    {
        $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);
        
        // 创建续费订单
        $order = Order::create([
            'order_no' => 'R' . date('YmdHis') . rand(1000, 9999),
            'user_id' => Auth::id(),
            'product_type' => 'subscription',
            'product_id' => $subscription->id,
            'amount' => $this->getRenewalPrice($subscription->plan),
            'status' => 'pending',
            'remark' => '续费：' . $subscription->plan,
        ]);

        return redirect()->route('payments.show', $order->order_no)
            ->with('success', '请完成支付');
    }

    /**
     * 升级订阅
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'new_plan' => 'required|in:monthly,yearly,lifetime',
        ]);

        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return back()->with('error', '没有有效的订阅');
        }

        // 计算升级差价
        $upgradePrice = $this->calculateUpgradePrice($subscription->plan, $request->new_plan);

        // 创建升级订单
        $order = Order::create([
            'order_no' => 'U' . date('YmdHis') . rand(1000, 9999),
            'user_id' => Auth::id(),
            'product_type' => 'subscription',
            'product_id' => $subscription->id,
            'amount' => $upgradePrice,
            'status' => 'pending',
            'remark' => "升级：{$subscription->plan} → {$request->new_plan}",
        ]);

        return redirect()->route('payments.show', $order->order_no)
            ->with('success', '请完成支付');
    }

    /**
     * 设置自动续费
     */
    public function toggleAutoRenew($id)
    {
        $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);
        
        $subscription->update([
            'auto_renew' => $subscription->auto_renew ? '0' : '1',
        ]);

        return back()->with('success', '自动续费已' . ($subscription->auto_renew ? '开启' : '关闭'));
    }

    /**
     * 取消订阅
     */
    public function cancel($id)
    {
        $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);
        
        $subscription->update([
            'status' => 'cancelled',
        ]);

        $this->notificationService->send(
            Auth::user(),
            '订阅已取消',
            '您的订阅已取消，到期后将不再续费。'
        );

        return back()->with('success', '订阅已取消');
    }

    /**
     * 获取续费价格
     */
    protected function getRenewalPrice($plan)
    {
        $prices = [
            'monthly' => 29,
            'yearly' => 199,
            'lifetime' => 999,
        ];
        return $prices[$plan] ?? 0;
    }

    /**
     * 计算升级差价
     */
    protected function calculateUpgradePrice($fromPlan, $toPlan)
    {
        $prices = [
            'monthly' => 29,
            'yearly' => 199,
            'lifetime' => 999,
        ];

        $fromPrice = $prices[$fromPlan] ?? 0;
        $toPrice = $prices[$toPlan] ?? 0;

        return max(0, $toPrice - $fromPrice);
    }
}
