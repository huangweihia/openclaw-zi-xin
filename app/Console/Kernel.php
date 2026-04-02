<?php

namespace App\Console;

use App\Models\EmailSetting;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // // 每日凌晨 2 点使用 OpenClaw 自动获取 AI 内容（文章/项目/职位/知识库）
        // $schedule->command('ai:fetch-daily')
        //          ->dailyAt('02:00')
        //          ->timezone('Asia/Shanghai')
        //          ->withoutOverlapping();

        /**
         * 邮件订阅：日报 / 周报（周一走周报模板）
         * 发送时刻与模板 key 见后台「系统设置」：email_send_time、email_digest_template_key、email_weekly_template_key
         * 依赖 scheduler 每分钟执行 `php artisan schedule:run`（时区 Asia/Shanghai）
         */
        $schedule->call(function (): void {
            Artisan::call('emails:send-scheduled', ['--limit' => '100']);
        })
            ->name('subscription-digest-emails')
            ->everyMinute()
            ->timezone('Asia/Shanghai')
            ->when(fn (): bool => $this->matchesConfiguredEmailSendTime())
            ->withoutOverlapping(10);
        
        // 每日上午 10 点发送 AI 副业项目推荐邮件（核心功能）
        $schedule->command('ai-projects:send-daily', ['--email' => '2801359160@qq.com'])
                 ->dailyAt('10:00')
                 ->timezone('Asia/Shanghai')
                 ->withoutOverlapping();
        
        // 每日上午 10 点发送职位推荐邮件（次要功能）
        $schedule->command('jobs:send-daily', ['--email' => '2801359160@qq.com'])
                 ->dailyAt('10:30')
                 ->timezone('Asia/Shanghai')
                 ->withoutOverlapping();
        
        // 每日上午 9 点发送企业微信 AI 日报（VIP 专属）
        $schedule->command('wechat:send-daily-digest')
                 ->dailyAt('09:00')
                 ->timezone('Asia/Shanghai')
                 ->withoutOverlapping();
    }

    /**
     * 当前上海时间是否与后台「邮件发送时间」一致（精确到分钟）
     */
    protected function matchesConfiguredEmailSendTime(): bool
    {
        $raw = trim((string) EmailSetting::get('email_send_time', '10:00'));
        try {
            $configured = Carbon::parse($raw, 'Asia/Shanghai')->format('H:i');
        } catch (\Throwable) {
            $configured = '10:00';
        }

        return now('Asia/Shanghai')->format('H:i') === $configured;
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
