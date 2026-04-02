<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplatePresetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 资讯/周报/欢迎/通知 与迁移 2026_03_28_150000 共用 database/data 文件。
     */
    public function run(): void
    {
        $templates = array_merge(
            require __DIR__.'/../data/email_templates_newsletter_and_system_presets.php',
            [
                // === 用户主页留言 · VIP 紧急通知（与迁移 2026_03_28_100001 一致） ===
                [
                    'name' => '主页留言 · VIP 紧急通知',
                    'key' => 'profile_message_urgent',
                    'subject' => '【紧急通知】{{profile_owner_name}} 回复了你的主页留言',
                    'content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 24px; }
        .box { background: linear-gradient(135deg, #fef3c7 0%, #fff7ed 100%); border: 1px solid #fbbf24; border-radius: 12px; padding: 20px; margin: 16px 0; }
        .label { font-size: 12px; color: #b45309; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .footer { color: #64748b; font-size: 12px; margin-top: 24px; }
        a.btn { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #fff; text-decoration: none; border-radius: 10px; font-weight: 700; margin-top: 12px; }
    </style>
</head>
<body>
    <p>你好 <strong>{{recipient_name}}</strong>，</p>
    <p>你在 <strong>{{profile_owner_name}}</strong> 个人主页留下的留言，对方已通过 <strong>VIP 紧急通知</strong> 向你发送本邮件。</p>
    <div class="box">
        <div class="label">你的留言摘要</div>
        <p style="margin: 8px 0 0;">{{message_excerpt}}</p>
    </div>
    <p>{{urgent_note}}</p>
    <p><a class="btn" href="{{profile_url}}">查看 TA 的主页</a></p>
    <p class="footer">本邮件由 AI 副业情报局系统代发。若不希望再收到此类邮件，请登录后在设置中调整通知偏好。</p>
</body>
</html>
HTML
                    ,
                    'variables' => [
                        'recipient_name',
                        'profile_owner_name',
                        'message_excerpt',
                        'urgent_note',
                        'profile_url',
                    ],
                    'is_active' => true,
                ],

                [
                    'name' => '主页留言 · 管理员自动通知',
                    'key' => 'profile_message_admin_auto',
                    'subject' => '【通知】{{profile_owner_name}} 已收到你的主页留言',
                    'content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 24px; }
        .box { background: linear-gradient(135deg, #eef2ff 0%, #faf5ff 100%); border: 1px solid #a5b4fc; border-radius: 12px; padding: 20px; margin: 16px 0; }
        .label { font-size: 12px; color: #4338ca; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .footer { color: #64748b; font-size: 12px; margin-top: 24px; }
        a.btn { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #fff; text-decoration: none; border-radius: 10px; font-weight: 700; margin-top: 12px; }
    </style>
</head>
<body>
    <p>你好 <strong>{{recipient_name}}</strong>，</p>
    <p>你在 <strong>{{profile_owner_name}}</strong> 的个人主页留下的留言，对方为 <strong>站点管理员</strong>，系统已<strong>自动</strong>向你发送本邮件以便你及时知晓。</p>
    <div class="box">
        <div class="label">你的留言摘要</div>
        <p style="margin: 8px 0 0;">{{message_excerpt}}</p>
    </div>
    <p>{{extra_note}}</p>
    <p><a class="btn" href="{{profile_url}}">查看 TA 的主页</a></p>
    <p class="footer">本邮件由 AI 副业情报局系统代发。若不希望再收到此类邮件，请登录后在「订阅偏好」中关闭系统通知。</p>
</body>
</html>
HTML
                    ,
                    'variables' => [
                        'recipient_name',
                        'profile_owner_name',
                        'message_excerpt',
                        'extra_note',
                        'profile_url',
                    ],
                    'is_active' => true,
                ],

                [
                    'name' => '主页留言 · 管理员发给用户通知',
                    'key' => 'profile_message_to_owner_from_admin',
                    'subject' => '【站点通知】管理员 {{admin_name}} 在你的主页留言了',
                    'content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 24px; }
        .box { background: linear-gradient(135deg, #eef2ff 0%, #faf5ff 100%); border: 1px solid #a5b4fc; border-radius: 12px; padding: 20px; margin: 16px 0; }
        .label { font-size: 12px; color: #4338ca; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .footer { color: #64748b; font-size: 12px; margin-top: 24px; }
        a.btn { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #fff; text-decoration: none; border-radius: 10px; font-weight: 700; margin-top: 12px; }
    </style>
</head>
<body>
    <p>你好 <strong>{{recipient_name}}</strong>，</p>
    <p>站点管理员 <strong>{{admin_name}}</strong> 在你的个人主页留下了一条留言，系统已<strong>自动</strong>发送本邮件以便你及时查看。</p>
    <div class="box">
        <div class="label">留言摘要</div>
        <p style="margin: 8px 0 0;">{{message_excerpt}}</p>
    </div>
    <p>{{extra_note}}</p>
    <p><a class="btn" href="{{profile_url}}">查看我的主页留言</a></p>
    <p class="footer">本邮件由 AI 副业情报局系统代发。若不希望再收到此类邮件，请登录后在「订阅偏好」中关闭系统通知。</p>
</body>
</html>
HTML
                    ,
                    'variables' => [
                        'recipient_name',
                        'admin_name',
                        'message_excerpt',
                        'extra_note',
                        'profile_url',
                    ],
                    'is_active' => true,
                ],

                [
                    'name' => 'VIP 到期提醒',
                    'key' => 'vip_expiry_reminder',
                    'subject' => '【提醒】您的 VIP 将于 {{days_remaining}} 天后到期',
                    'content' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 24px; }
        .box { background: linear-gradient(135deg, #eef2ff 0%, #faf5ff 100%); border: 1px solid #a5b4fc; border-radius: 12px; padding: 20px; margin: 16px 0; }
        a.btn { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #fff; text-decoration: none; border-radius: 10px; font-weight: 700; margin-top: 12px; }
        .muted { color: #64748b; font-size: 13px; margin-top: 24px; }
    </style>
</head>
<body>
    <p>你好 <strong>{{recipient_name}}</strong>，</p>
    <p>你的 <strong>VIP 会员</strong>将在 <strong>{{expiry_date}}</strong> 到期（剩余约 <strong>{{days_remaining}}</strong> 天）。</p>
    <div class="box">
        <p style="margin:0;">为避免影响 VIP 专属内容、投稿与联系方式等权益，请在到期前续费。</p>
    </div>
    <p><a class="btn" href="{{vip_url}}">前往续费 / 开通 VIP</a></p>
    <p><a href="{{dashboard_url}}" style="color:#6366f1;">进入个人中心</a></p>
    <p class="muted">本邮件由 AI 副业情报局后台发送。若你已续费，请忽略。</p>
</body>
</html>
HTML
                    ,
                    'variables' => [
                        'recipient_name',
                        'expiry_date',
                        'days_remaining',
                        'vip_url',
                        'dashboard_url',
                    ],
                    'is_active' => true,
                ],
            ]
        );

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }

        $this->command->info('✅ 邮件模板预设已导入！');
        $this->command->info('   - 每日资讯日报（daily_digest）与经典/现代日报、周报、欢迎、系统通知（与 migrate 同源）');
        $this->command->info('   - 主页留言 · VIP 紧急通知（profile_message_urgent）');
        $this->command->info('   - 主页留言 · 管理员相关（profile_message_admin_auto / profile_message_to_owner_from_admin）');
        $this->command->info('   - VIP 到期提醒（vip_expiry_reminder）');
    }
}
