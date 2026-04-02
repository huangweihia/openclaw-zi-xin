<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        EmailTemplate::updateOrCreate(
            ['key' => 'vip_expiry_reminder'],
            [
                'name' => 'VIP 到期提醒',
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
            ]
        );
    }

    public function down(): void
    {
        EmailTemplate::where('key', 'vip_expiry_reminder')->delete();
    }
};
