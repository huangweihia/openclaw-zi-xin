<?php

namespace Database\Seeders;

use App\Models\EmailSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 演示用邮件订阅（与 TestDataSeeder 中的 vip 测试邮箱对齐，便于本地测定时任务）。
 */
class DemoEmailSubscriptionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📬 写入演示邮件订阅...');

        $rows = [
            [
                'email' => '2801359160@qq.com',
                'subscribed_to_daily' => true,
                'subscribed_to_weekly' => true,
                'subscribed_to_notifications' => true,
            ],
            [
                'email' => '2424951090@qq.com',
                'subscribed_to_daily' => true,
                'subscribed_to_weekly' => true,
                'subscribed_to_notifications' => false,
            ]
            
        ];

        foreach ($rows as $sub) {
            $userId = User::query()->where('email', $sub['email'])->value('id');

            EmailSubscription::updateOrCreate(
                ['email' => $sub['email']],
                array_merge($sub, [
                    'user_id' => $userId,
                    'unsubscribed_at' => null,
                ])
            );

            $this->command->info("  ✅ {$sub['email']}");
        }
    }
}
