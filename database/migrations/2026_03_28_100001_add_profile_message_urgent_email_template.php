<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        EmailTemplate::updateOrCreate(
            ['key' => 'profile_message_urgent'],
            [
                'name' => '主页留言 · VIP 紧急通知',
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
            ]
        );
    }

    public function down(): void
    {
        EmailTemplate::where('key', 'profile_message_urgent')->delete();
    }
};
