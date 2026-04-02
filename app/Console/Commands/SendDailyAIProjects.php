<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use App\Models\Project as ProjectModel;
use App\Services\AISideProjectService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyAIProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai-projects:send-daily {--email=} {--test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送每日 AI 副业项目推荐邮件（AI & 副业资讯日报）';

    /**
     * Execute the console command.
     */
    public function handle(AISideProjectService $projectService): int
    {
        $this->info('🤖 开始生成 AI & 副业资讯日报...');
        
        // 获取每日资讯（3 个板块）
        $data = $projectService->getDailyDigest();
        
        $this->info('✅ 热门项目：' . count($data['hot_projects']) . ' 个');
        $this->info('✅ 副业灵感：' . count($data['side_hustles']) . ' 个');
        $this->info('✅ 学习资源：' . count($data['learning_resources']) . ' 个');
        
        // 保存到数据库（只保存 GitHub 项目，过滤 url 为 # 的）
        foreach ($data['hot_projects'] as $projectData) {
            if (empty($projectData['url']) || $projectData['url'] === '#') {
                continue;
            }
            
            ProjectModel::firstOrCreate(
                ['name' => $projectData['name']],
                [
                    'description' => $projectData['description'] ?? '',
                    'url' => $projectData['url'],
                    'status' => 'planning',
                ]
            );
        }
        
        $this->info('💾 项目已保存到数据库');
        
        // 生成邮件内容
        $email = $this->option('email') ?? config('mail.from.address');
        $content = $projectService->generateDailyDigestEmail($data);
        
        // 如果是测试模式，显示摘要
        if ($this->option('test')) {
            $this->info('📧 测试模式 - 邮件已生成（内容过长不显示）');
            $this->info('HTML 长度：' . strlen($content) . ' 字符');
            return self::SUCCESS;
        }
        
        // 记录邮件日志
        $date = now()->format('Y-m-d');
        $emailLog = EmailLog::create([
            'recipient' => $email,
            'subject' => "🤖 AI & 副业资讯日报 - {$date}",
            'content' => $content,
            'type' => 'job_daily',
            'status' => 'pending',
        ]);
        
        // 发送邮件
        try {
            Mail::send([], [], function ($message) use ($email, $date, $content) {
                $message->to($email)
                        ->subject("🤖 AI & 副业资讯日报 - {$date}")
                        ->html($content);
            });
            
            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            
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
