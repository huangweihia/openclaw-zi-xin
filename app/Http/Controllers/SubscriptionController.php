<?php

namespace App\Http\Controllers;

use App\Models\EmailSubscription;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    /**
     * 显示退订页面
     */
    public function showUnsubscribe(string $token)
    {
        $subscription = EmailSubscription::getByToken($token);
        
        if (!$subscription) {
            abort(404, '订阅不存在');
        }
        
        return view('subscriptions.unsubscribe', compact('subscription'));
    }

    /**
     * 处理退订
     */
    public function unsubscribe(Request $request, string $token)
    {
        $subscription = EmailSubscription::getByToken($token);
        
        if (!$subscription) {
            abort(404, '订阅不存在');
        }
        
        $type = $request->input('type', 'all');
        
        match ($type) {
            'daily' => $subscription->update(['subscribed_to_daily' => false]),
            'weekly' => $subscription->update(['subscribed_to_weekly' => false]),
            'notifications' => $subscription->update(['subscribed_to_notifications' => false]),
            default => $subscription->unsubscribeAll(),
        };
        
        return view('subscriptions.unsubscribed', compact('subscription', 'type'));
    }

    /**
     * 重新订阅
     */
    public function resubscribe(string $token)
    {
        $subscription = EmailSubscription::getByToken($token);
        
        if (!$subscription) {
            abort(404, '订阅不存在');
        }
        
        $subscription->resubscribe();
        
        return view('subscriptions.resubscribed', compact('subscription'));
    }

    /**
     * 显示订阅偏好设置（需要登录）
     */
    public function preferences()
    {
        $user = auth()->user();
        $subscription = EmailSubscription::getByEmail($user->email);
        
        if (!$subscription) {
            $subscription = EmailSubscription::create([
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }
        
        return view('subscriptions.preferences', compact('subscription'));
    }

    /**
     * 更新订阅偏好（支持 JSON/AJAX：请求头 Accept: application/json）
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        $subscription = EmailSubscription::getByEmail($user->email);

        if (! $subscription) {
            $subscription = EmailSubscription::create([
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        try {
            $request->validate([
                'subscribed_to_daily' => 'sometimes|boolean',
                'subscribed_to_weekly' => 'sometimes|boolean',
                'subscribed_to_notifications' => 'sometimes|boolean',
            ]);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '验证失败',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        $subscription->update([
            'subscribed_to_daily' => $request->boolean('subscribed_to_daily'),
            'subscribed_to_weekly' => $request->boolean('subscribed_to_weekly'),
            'subscribed_to_notifications' => $request->boolean('subscribed_to_notifications'),
            'unsubscribed_at' => null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => '订阅偏好已更新',
                'subscription' => [
                    'subscribed_to_daily' => $subscription->subscribed_to_daily,
                    'subscribed_to_weekly' => $subscription->subscribed_to_weekly,
                    'subscribed_to_notifications' => $subscription->subscribed_to_notifications,
                ],
            ]);
        }

        return redirect()
            ->route('subscriptions.preferences')
            ->with('success', '订阅偏好已更新');
    }
}
