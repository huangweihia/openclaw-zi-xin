<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use App\Models\JobListing;
use App\Services\BossJobService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:send-daily {--email=} {--test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送每日 PHP 职位推荐邮件';

    /**
     * Execute the console command.
     */
    public function handle(BossJobService $jobService): int
    {
        $this->info('🚀 开始搜索职位...');
        
        // 搜索职位
        $jobs = $jobService->searchJobs('杭州', '15-30k');
        
        $this->info('✅ 找到 ' . count($jobs) . ' 个职位');
        
        if (empty($jobs)) {
            $this->warn('⚠️ 未找到职位，跳过邮件发送');
            return self::SUCCESS;
        }
        
        // 保存到数据库
        foreach ($jobs as $jobData) {
            JobListing::create($jobData);
        }
        
        $this->info('💾 职位已保存到数据库');
        
        // 生成邮件内容
        $email = $this->option('email') ?? config('mail.from.address');
        $content = $jobService->generateJobSummary($jobs);
        
        // 如果是测试模式，显示内容
        if ($this->option('test')) {
            $this->info('📧 测试模式 - 邮件内容：');
            $this->line($content);
            return self::SUCCESS;
        }
        
        // 记录邮件日志
        $emailLog = EmailLog::create([
            'recipient' => $email,
            'subject' => '📌 今日 PHP 职位推荐 - ' . now()->format('Y-m-d'),
            'content' => $content,
            'type' => 'job_daily',
            'status' => 'pending',
        ]);
        
        // 发送邮件
        try {
            Mail::raw($content, function ($message) use ($email, $emailLog) {
                $message->to($email)
                        ->subject('📌 今日 PHP 职位推荐 - ' . now()->format('Y-m-d'));
            });
            
            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            
            // 标记职位为已发送
            JobListing::where('created_at', '>=', now()->subMinutes(5))
                      ->update(['is_sent' => true]);
            
            $this->info("📧 邮件已发送至：{$email}");
        } catch (\Exception $e) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            $this->error('❌ 邮件发送失败：' . $e->getMessage());
            return self::FAILURE;
        }
        
        return self::SUCCESS;
    }
}
