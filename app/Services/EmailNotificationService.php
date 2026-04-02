<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\EmailSubscription;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * 模板邮件（欢迎、VIP 提醒、紧急通知等）默认仅在用户开启「系统通知」订阅时发送。
     * $ignoreSubscriptionPreference 为 true 时跳过校验（如管理员主页留言自动通知等重要场景）。
     * 日报/周报由独立命令按 subscribed_to_daily / subscribed_to_weekly 筛选，不走本方法。
     */
    public function sendFromTemplateByKey(string $templateKey, User $user, array $extraData = [], ?string $type = null, bool $ignoreSubscriptionPreference = false): bool
    {
        if (! $ignoreSubscriptionPreference && ! EmailSubscription::wantsSystemNotifications($user)) {
            return false;
        }

        $template = EmailTemplate::query()
            ->where('key', $templateKey)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return false;
        }

        $data = array_merge([
            'name' => $user->name,
            'email' => $user->email,
            'date' => now()->format('Y-m-d H:i'),
            'dashboard_url' => url('/dashboard'),
            'vip_url' => route('vip'),
        ], $extraData);

        $subject = $this->replaceVariables($template->subject, $data);
        $content = $template->render($data);

        $emailLog = EmailLog::create([
            'recipient' => $user->email,
            'subject' => $subject,
            'content' => $content,
            'type' => $type ?? $templateKey,
            'template_id' => $template->id,
            'status' => 'pending',
        ]);

        try {
            // 使用 html() 方法发送 HTML 邮件
            Mail::html($content, function ($message) use ($user, $subject): void {
                $message->to($user->email)
                    ->subject($subject)
                    ->from(
                        config('mail.from.address', '2801359160@qq.com'),
                        config('mail.from.name', 'AI 副业情报局')
                    );
            });

            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function replaceVariables(string $text, array $data): string
    {
        foreach ($data as $key => $value) {
            $text = str_replace('{{' . $key . '}}', (string) $value, $text);
        }

        return $text;
    }
}
