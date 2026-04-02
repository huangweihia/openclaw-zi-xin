<?php

namespace App\Console\Commands;

use App\Models\EmailSetting;
use App\Models\EmailSubscription;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use App\Models\Project;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-weekly-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每周一自动发送 AI 资讯周报';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📧 开始发送每周 AI 资讯周报...');
        
        // 获取订阅周报的用户
        $subscriptions = EmailSubscription::where('subscribed_to_weekly', true)
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
            ->get();
        
        if ($subscriptions->isEmpty()) {
            $this->warn('⚠️ 没有订阅周报的用户');
            return Command::SUCCESS;
        }
        
        $this->info("📋 共有 {$subscriptions->count()} 位订阅用户");

        $base = now();
        $weekStart = $base->copy()->startOfWeek();
        $weekEnd = $base->copy()->endOfWeek();
        $weekRange = $weekStart->format('m-d').' ~ '.$weekEnd->format('m-d');
        $weekRangeLong = $weekStart->format('Y-m-d').' ~ '.$weekEnd->format('Y-m-d');
        $weekLabel = $base->format('Y-m-d').' 第'.$base->week.'周';

        // 获取本周热门项目
        $topProjects = Project::whereBetween('created_at', [$weekStart, $weekEnd])
            ->orderBy('stars', 'desc')
            ->limit(10)
            ->get();

        // 获取本周热门文章
        $topArticles = Article::where('is_published', true)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();
        
        $weeklyKey = EmailSetting::getWeeklyTemplateKey();

        // 获取模板（与系统设置中的「周报模板」一致）
        $template = EmailTemplate::where('key', $weeklyKey)
            ->where('is_active', true)
            ->first();
        
        if (!$template) {
            $this->error('❌ 未找到周报模板');
            return Command::FAILURE;
        }
        
        $success = 0;
        $failed = 0;
        
        foreach ($subscriptions as $subscription) {
            try {
                $data = [
                    'week' => $weekLabel,
                    'week_range' => $weekRange,
                    'week_range_long' => $weekRangeLong,
                    'name' => $subscription->user?->name ?? '朋友',
                    'email' => $subscription->email,
                    'top_projects' => $this->renderProjects($topProjects),
                    'articles' => $this->renderArticles($topArticles),
                    'projects' => $this->renderProjects($topProjects),
                    'side_hustles' => $this->renderArticles($topArticles),
                    'projects_count' => (string) $topProjects->count(),
                    'articles_count' => (string) $topArticles->count(),
                    'tips_count' => '3',
                    'unsubscribe_url' => $subscription->getUnsubscribeUrl(),
                ];

                $content = $template->render($data);
                $subject = str_replace(
                    ['{{week}}', '{{week_range}}', '{{week_range_long}}'],
                    [$weekLabel, $weekRange, $weekRangeLong],
                    $template->subject
                );
                
                // 记录邮件日志
                $emailLog = EmailLog::create([
                    'recipient' => $subscription->email,
                    'subject' => $subject,
                    'content' => $content,
                    'type' => 'weekly_digest',
                    'template_id' => $template->id,
                    'status' => 'pending',
                ]);
                
                Mail::html($content, function ($message) use ($subscription, $subject) {
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
            return '<p>本周暂无新项目</p>';
        }
        
        $html = '<ol style="padding-left: 20px;">';
        foreach ($projects as $project) {
            $html .= '<li style="margin-bottom: 15px;">';
            $html .= '<strong>' . e($project->name) . '</strong> ';
            $html .= '<span style="color: #64748b; font-size: 13px;">⭐ ' . number_format($project->stars) . '</span><br>';
            $html .= '<span style="color: #94a3b8; font-size: 13px;">' . e($project->description) . '</span>';
            $html .= '</li>';
        }
        $html .= '</ol>';
        
        return $html;
    }
    
    /**
     * 渲染文章列表
     */
    private function renderArticles($articles): string
    {
        if ($articles->isEmpty()) {
            return '<p>本周暂无新文章</p>';
        }
        
        $html = '<ol style="padding-left: 20px;">';
        foreach ($articles as $article) {
            $html .= '<li style="margin-bottom: 12px;">';
            $html .= '<strong>' . e($article->title) . '</strong><br>';
            $html .= '<span style="color: #94a3b8; font-size: 13px;">👁️ ' . number_format($article->view_count) . ' 次阅读</span>';
            $html .= '</li>';
        }
        $html .= '</ol>';
        
        return $html;
    }
}
