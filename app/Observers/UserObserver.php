<?php

namespace App\Observers;

use App\Models\User;
use App\Services\EmailNotificationService;

class UserObserver
{
    public function updated(User $user): void
    {
        $becameVipByRole = $user->wasChanged('role') && $user->role === 'vip';
        $becameVipBySubscription = $user->wasChanged('subscription_ends_at')
            && $user->subscription_ends_at
            && $user->subscription_ends_at->isFuture();

        if (!$becameVipByRole && !$becameVipBySubscription) {
            return;
        }

        $service = app(EmailNotificationService::class);

        // 优先使用 vip_upgrade 模板；若后台未配置则回退到 welcome 模板
        $sent = $service->sendFromTemplateByKey(
            'vip_upgrade',
            $user,
            [
                'vip_end_date' => optional($user->subscription_ends_at)->format('Y-m-d') ?: '长期',
            ],
            'vip_upgrade'
        );

        if (!$sent) {
            $service->sendFromTemplateByKey(
                'welcome',
                $user,
                [
                    'vip_end_date' => optional($user->subscription_ends_at)->format('Y-m-d') ?: '长期',
                ],
                'vip_upgrade'
            );
        }
    }
}
