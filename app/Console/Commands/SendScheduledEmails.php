<?php

namespace App\Console\Commands;

use App\Services\SubscriptionDigestMailer;
use Illuminate\Console\Command;

class SendScheduledEmails extends Command
{
    protected $signature = 'emails:send-scheduled
                            {--limit=100 : 每次发送的最大数量}
                            {--email= : 仅向该邮箱发送一封（用于测试，须为已订阅且未退订的地址）}';

    protected $description = '发送定时邮件（日报/周报）；时刻与模板见后台系统设置 + email_settings 表';

    public function handle(SubscriptionDigestMailer $mailer): int
    {
        $limit = (int) $this->option('limit');
        $onlyEmail = $this->option('email');
        $today = now();
        $isMonday = $today->isMonday();

        $this->info("开始发送定时邮件，限制数量：{$limit}");

        if ($onlyEmail) {
            $this->comment("单邮箱模式：{$onlyEmail}");
        }
        if ($onlyEmail && $isMonday) {
            $this->warn('测试模式：单邮箱仍使用日报模板（与生产周一发全员周报不同）');
        }

        $result = $mailer->runBatch($limit, $onlyEmail ?: null);

        foreach ($result['lines'] as $line) {
            if (str_starts_with($line, '✓')) {
                $this->line($line);
            } elseif (str_starts_with($line, '✗')) {
                $this->error($line);
            } else {
                $this->warn($line);
            }
        }

        $this->info("发送完成！成功：{$result['sent']}, 失败：{$result['failed']}");

        return 0;
    }
}
