<?php

namespace App\Console\Commands;

use App\Models\EmailSubscription;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use App\Models\Project;
use App\Models\Article;
use App\Models\JobListing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-daily-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每日自动发送 AI 资讯日报';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📧 开始发送每日 AI 资讯日报...');
        
        // 获取订阅日报的用户
        $subscriptions = EmailSubscription::where('subscribed_to_daily', true)
            ->whereNull('unsubscribed_at')
            ->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('role', 'vip')
                        ->orWhere(function ($normal) {
                            $normal->where('role', '!=', 'vip')
                                ->where('created_at', '>=', now()->subDays(3));
                        })
                        ->orWhereNull('role');
                })
                ->orWhereNull('user_id');
            })
            ->get()
            ->unique(fn (EmailSubscription $item): string => mb_strtolower(trim((string) $item->email)))
            ->values();
        
        if ($subscriptions->isEmpty()) {
            $this->warn('⚠️ 没有订阅日报的用户');
            return Command::SUCCESS;
        }
        
        $this->info("📋 共有 {$subscriptions->count()} 位订阅用户");
        
        // 获取今日热门项目
        $hotProjects = Project::whereDate('created_at', today())
            ->orderBy('stars', 'desc')
            ->limit(5)
            ->get();
        
        // 获取热门文章
        $hotArticles = Article::where('is_published', true)
            ->whereDate('created_at', today())
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get();
        
        // 获取最新职位
        $newJobs = JobListing::whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // 获取模板
        $template = EmailTemplate::where('key', 'daily_digest_classic')
            ->where('is_active', true)
            ->first();
        
        if (!$template) {
            $this->error('❌ 未找到日报模板');
            return Command::FAILURE;
        }
        
        $success = 0;
        $failed = 0;
        
        foreach ($subscriptions as $subscription) {
            try {
                $alreadySent = EmailLog::query()
                    ->where('recipient', $subscription->email)
                    ->where('type', 'daily_digest')
                    ->where('status', 'sent')
                    ->whereDate('sent_at', now()->toDateString())
                    ->exists();

                if ($alreadySent) {
                    $this->warn("⏭️ 已跳过（今日已发送）：{$subscription->email}");
                    continue;
                }

                // 渲染邮件内容
                $data = [
                    'date' => now()->format('Y-m-d'),
                    'name' => $subscription->user?->name ?? '朋友',
                    'email' => $subscription->email,
                    'projects' => $this->renderProjects($hotProjects),
                    'side_hustles' => $this->renderArticles($hotArticles),
                    'resources' => $this->renderJobs($newJobs),
                    'unsubscribe_url' => url('/unsubscribe/' . $subscription->unsubscribe_token),
                    'preferences_url' => url('/subscriptions/preferences'),
                ];
                
                $content = $template->render($data);
                $subject = str_replace('{{date}}', now()->format('Y-m-d'), $template->subject);
                
                // 记录邮件日志
                $emailLog = EmailLog::create([
                    'recipient' => $subscription->email,
                    'subject' => $subject,
                    'content' => $content,
                    'type' => 'daily_digest',
                    'template_id' => $template->id,
                    'status' => 'pending',
                ]);
                
                // 发送邮件
                Mail::raw($content, function ($message) use ($subscription, $subject) {
                    $message->to($subscription->email)
                            ->subject($subject)
                            ->from(config('mail.from.address', '2801359160@qq.com'), 
                                   config('mail.from.name', 'AI 副业情报局'));
                });
                
                $emailLog->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                
                $success++;
                $this->info("✅ 已发送给：{$subscription->email}");
                
            } catch (\Exception $e) {
                $failed++;
                $this->error("❌ 发送失败 {$subscription->email}: {$e->getMessage()}");
                
                if (isset($emailLog)) {
                    $emailLog->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }
        }
        
        $this->info('');
        $this->info('📊 发送完成统计：');
        $this->info("   ✅ 成功：{$success} 封");
        $this->info("   ❌ 失败：{$failed} 封");
        
        return Command::SUCCESS;
    }
    
    /**
     * 渲染项目列表
     */
    private function renderProjects($projects): string
    {
        if ($projects->isEmpty()) {
            return '<p>暂无新项目</p>';
        }
        
        $html = '<ul style="list-style: none; padding: 0;">';
        foreach ($projects as $project) {
            $html .= '<li style="margin-bottom: 15px; padding: 12px; background: rgba(99, 102, 241, 0.1); border-radius: 8px;">';
            $html .= '<strong style="color: #6366f1;">' . e($project->name) . '</strong><br>';
            $html .= '<span style="font-size: 13px; color: #94a3b8;">' . e($project->description) . '</span><br>';
            $html .= '<span style="font-size: 12px; color: #64748b;">⭐ ' . number_format($project->stars) . ' | 🔗 <a href="' . e($project->url) . '" style="color: #6366f1;">查看详情</a></span>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        
        return $html;
    }
    
    /**
     * 渲染文章列表
     */
    private function renderArticles($articles): string
    {
        if ($articles->isEmpty()) {
            return '<p>暂无新文章</p>';
        }
        
        $html = '<ul style="list-style: none; padding: 0;">';
        foreach ($articles as $article) {
            $html .= '<li style="margin-bottom: 15px;">';
            $html .= '<strong style="color: #8b5cf6;">' . e($article->title) . '</strong><br>';
            $html .= '<span style="font-size: 13px; color: #94a3b8;">' . e($article->summary) . '</span>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        
        return $html;
    }
    
    /**
     * 渲染职位列表
     */
    private function renderJobs($jobs): string
    {
        if ($jobs->isEmpty()) {
            return '<p>暂无新职位</p>';
        }
        
        $html = '<ul style="list-style: none; padding: 0;">';
        foreach ($jobs as $job) {
            $html .= '<li style="margin-bottom: 15px; padding: 12px; background: rgba(16, 185, 129, 0.1); border-radius: 8px;">';
            $html .= '<strong style="color: #10b981;">' . e($job->title) . '</strong><br>';
            $html .= '<span style="font-size: 13px; color: #94a3b8;">' . e($job->company_name) . ' | ' . e($job->salary) . ' | ' . e($job->city) . '</span><br>';
            $html .= '<span style="font-size: 12px; color: #64748b;">🔗 <a href="' . e($job->source_url) . '" style="color: #10b981;">申请职位</a></span>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        
        return $html;
    }
}
