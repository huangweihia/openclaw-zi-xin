<?php

namespace App\Filament\Pages;

use App\Models\EmailSetting;
use App\Models\EmailLog;
use App\Models\EmailSubscription;
use App\Models\EmailTemplate;
use App\Models\Project;
use App\Models\Article;
use App\Models\JobListing;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class EmailManager extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static string $view = 'filament.pages.email-manager';
    protected static ?string $navigationLabel = '邮件管理';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationGroup = '邮件系统';
    
    public ?string $recipient = '';
    public ?string $sendTime = '10:00';
    public array $recipients = [];
    public ?string $bulkEmails = '';
    public array $selectedForBulk = [];
    
    // 模态窗相关
    public ?string $modalAction = null;
    public ?string $modalEmail = null;
    public ?string $modalType = null;
    public array $selectedTemplates = ['daily_digest_classic'];
    public bool $showModal = false;
    public bool $showTemplateModal = false;
    public bool $isLoading = false;

    public function mount(): void
    {
        $this->recipients = EmailSetting::getRecipients();
        $this->sendTime = EmailSetting::getSendTime();
        $this->recipient = '';
    }

    public function addRecipient(): void
    {
        if (empty($this->recipient)) {
            Notification::make()
                ->title('请输入邮箱地址')
                ->warning()
                ->send();
            return;
        }

        if (!filter_var($this->recipient, FILTER_VALIDATE_EMAIL)) {
            Notification::make()
                ->title('邮箱格式不正确')
                ->danger()
                ->send();
            return;
        }

        if (in_array($this->recipient, $this->recipients)) {
            Notification::make()
                ->title('该邮箱已存在')
                ->warning()
                ->send();
            return;
        }

        $this->recipients[] = $this->recipient;
        EmailSetting::set('email_recipients', json_encode($this->recipients), '邮件接收人列表');
        
        EmailSubscription::updateOrCreate(
            ['email' => $this->recipient],
            [
                'subscribed_to_daily' => true,
                'subscribed_to_weekly' => true,
                'subscribed_to_notifications' => true,
                'unsubscribed_at' => null,
            ]
        );
        
        $this->recipient = '';
        $this->recipients = EmailSetting::getRecipients();
        
        Notification::make()
            ->title('✅ 添加成功')
            ->body('已添加到收件人列表，默认订阅所有类型邮件')
            ->success()
            ->send();
    }

    public function removeRecipient(string $email): void
    {
        $this->recipients = array_values(array_filter($this->recipients, fn($e) => $e !== $email));
        EmailSetting::set('email_recipients', json_encode($this->recipients), '邮件接收人列表');
        
        $subscription = EmailSubscription::where('email', $email)->first();
        if ($subscription) {
            $subscription->delete();
        }
        
        $this->recipients = EmailSetting::getRecipients();
        
        Notification::make()
            ->title('✅ 删除成功')
            ->success()
            ->send();
    }

    public function getSubscriptionStatus(string $email): array
    {
        $subscription = EmailSubscription::where('email', $email)->first();
        
        if (!$subscription) {
            return [
                'daily' => true,
                'weekly' => true,
                'notifications' => true,
                'exists' => false,
            ];
        }
        
        return [
            'daily' => $subscription->subscribed_to_daily && !$subscription->unsubscribed_at,
            'weekly' => $subscription->subscribed_to_weekly && !$subscription->unsubscribed_at,
            'notifications' => $subscription->subscribed_to_notifications && !$subscription->unsubscribed_at,
            'exists' => true,
        ];
    }

    public function confirmAction(string $action, string $email = null, string $type = null): void
    {
        $this->modalAction = $action;
        $this->modalEmail = $email;
        $this->modalType = $type;
        $this->showModal = true;
    }

    public function toggleSubscription(string $email, string $type): void
    {
        try {
            $subscription = EmailSubscription::firstOrCreate(
                ['email' => $email],
                [
                    'subscribed_to_daily' => true,
                    'subscribed_to_weekly' => true,
                    'subscribed_to_notifications' => true,
                    'unsubscribe_token' => \Illuminate\Support\Str::random(32),
                ]
            );
            
            $subscription->update([
                $type => !$subscription->{$type},
            ]);
            
            $this->recipients = EmailSetting::getRecipients();
            
            Notification::make()
                ->title('✅ 已更新')
                ->body('订阅偏好已保存')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ 更新失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function executeModalAction(): void
    {
        $this->isLoading = true;
        
        try {
            match ($this->modalAction) {
                'delete' => $this->removeRecipient($this->modalEmail),
                'send_test' => $this->sendTestEmailToSingle($this->modalEmail),
                'bulk_delete' => $this->bulkDelete(),
                default => null,
            };
            
            $this->showModal = false;
            $this->modalAction = null;
            $this->modalEmail = null;
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ 操作失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isLoading = false;
        }
    }

    public function openTemplateModal(): void
    {
        if (empty($this->recipients)) {
            Notification::make()
                ->title('⚠️ 请先添加收件人')
                ->warning()
                ->send();
            return;
        }
        
        $this->showTemplateModal = true;
        $this->selectedTemplates = ['daily_digest_classic'];
    }

    public function sendWithTemplate(): void
    {
        if (empty($this->recipients)) {
            Notification::make()
                ->title('⚠️ 请先添加收件人')
                ->warning()
                ->send();
            return;
        }
        
        if (empty($this->selectedTemplates)) {
            Notification::make()
                ->title('⚠️ 请至少选择一个模板')
                ->warning()
                ->send();
            return;
        }
        
        $this->isLoading = true;
        
        try {
            $totalSuccess = 0;
            $totalFailed = 0;
            
            // 获取所有选中的模板
            $templates = EmailTemplate::whereIn('key', $this->selectedTemplates)
                ->where('is_active', true)
                ->get();
            
            if ($templates->isEmpty()) {
                throw new \Exception('选中的模板不存在或未启用');
            }
            
            // 获取最新数据（按排序 + ID 倒序）
            $hotProjects = Project::orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
            
            $hotArticles = Article::where('is_published', true)
                ->orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
            
            $newJobs = JobListing::orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
            
            // 给每个收件人发送
            foreach ($this->recipients as $email) {
                $success = 0;
                
                foreach ($templates as $template) {
                    $emailLog = null;
                    try {
                        $subscription = EmailSubscription::where('email', $email)->first();

                        if (! $this->canSendTemplate($template, $subscription)) {
                            continue;
                        }

                        $data = $this->prepareEmailData($email, $subscription, $hotProjects, $hotArticles, $newJobs);

                        $content = $this->renderTemplateContent($template, $data);
                        $subject = $this->getSubject($template, $data);

                        $emailLog = EmailLog::create([
                            'recipient' => $email,
                            'subject' => $subject,
                            'content' => $content,
                            'type' => $template->key,
                            'template_id' => $template->id,
                            'status' => 'pending',
                        ]);

                        Mail::html($content, function ($message) use ($email, $subject): void {
                            $message->to($email)
                                ->subject($subject)
                                ->from(config('mail.from.address', '2801359160@qq.com'),
                                    config('mail.from.name', 'AI 副业情报局'));
                        });

                        $emailLog->update(['status' => 'sent', 'sent_at' => now()]);
                        $success++;
                    } catch (\Exception $e) {
                        $totalFailed++;
                        if ($emailLog !== null) {
                            $emailLog->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                        }
                    }
                }
                
                $totalSuccess += $success;
            }
            
            $this->showTemplateModal = false;
            
            Notification::make()
                ->title('✅ 邮件已发送')
                ->body("成功发送 {$totalSuccess} 封邮件")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ 发送失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isLoading = false;
        }
    }
    
    private function canSendTemplate($template, $subscription): bool
    {
        // 邮件管理里手动维护的收件人：无订阅记录也允许发送（此前无记录会导致日报/周报全部被跳过）
        if (! $subscription) {
            return true;
        }

        $k = (string) $template->key;
        if ($k === 'weekly_summary' || str_contains($k, 'weekly')) {
            return $subscription->isSubscribedToWeekly();
        }
        if ($k === 'notification') {
            return $subscription->isSubscribedToNotifications();
        }
        if (str_contains($k, 'digest') || str_contains($k, 'daily')) {
            return $subscription->isSubscribedToDaily();
        }

        return true;
    }
    
    private function prepareEmailData(string $email, $subscription, $projects, $articles, $jobs): array
    {
        $rp = $this->renderProjects($projects);
        $ra = $this->renderArticles($articles);

        return [
            'date' => now()->format('Y-m-d'),
            'week_range' => now()->startOfWeek()->format('m-d').' ~ '.now()->endOfWeek()->format('m-d'),
            'week_range_long' => now()->startOfWeek()->format('Y-m-d').' ~ '.now()->endOfWeek()->format('Y-m-d'),
            'name' => $subscription?->user?->name ?? '朋友',
            'email' => $email,
            'projects' => $rp,
            'side_hustles' => $ra,
            'resources' => $this->renderJobs($jobs),
            'top_projects' => $rp,
            'articles' => $ra,
            'projects_count' => (string) Project::query()->where('is_featured', true)->count(),
            'articles_count' => (string) Article::query()->where('is_published', true)->count(),
            'tips_count' => '3',
            'issue_number' => (string) now()->dayOfYear,
            'unsubscribe_url' => $subscription
                ? url('/unsubscribe/'.$subscription->unsubscribe_token)
                : rtrim((string) config('app.url'), '/'),
            'preferences_url' => url('/subscriptions/preferences'),
        ];
    }
    
    private function renderTemplateContent($template, array $data): string
    {
        $content = (string) $template->content;
        $map = [];
        foreach ($data as $key => $value) {
            $map['{{'.$key.'}}'] = (string) $value;
        }
        $content = str_replace(array_keys($map), array_values($map), $content);

        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', function (array $m) use ($map): string {
            $key = '{{'.$m[1].'}}';

            return $map[$key] ?? $m[0];
        }, $content) ?? $content;
    }

    private function getSubject($template, array $data): string
    {
        $subject = (string) $template->subject;
        foreach ($data as $key => $value) {
            $subject = str_replace('{{'.$key.'}}', (string) $value, $subject);
        }

        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', function (array $m) use ($data): string {
            $k = $m[1];

            return isset($data[$k]) ? (string) $data[$k] : $m[0];
        }, $subject) ?? $subject;
    }
    
    private function renderProjects($projects): string
    {
        if ($projects->isEmpty()) return '<p>暂无新项目</p>';
        
        $html = '<ul style="list-style: none; padding: 0;">';
        foreach ($projects as $project) {
            $html .= '<li style="margin-bottom: 15px; padding: 12px; background: rgba(99, 102, 241, 0.1); border-radius: 8px;">';
            $html .= '<strong style="color: #6366f1;">' . e($project->name) . '</strong><br>';
            $html .= '<span style="font-size: 13px; color: #94a3b8;">' . e($project->description) . '</span><br>';
            $html .= '<span style="font-size: 12px; color: #64748b;">⭐ ' . number_format($project->stars) . '</span>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    
    private function renderArticles($articles): string
    {
        if ($articles->isEmpty()) return '<p>暂无新文章</p>';
        
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
    
    private function renderJobs($jobs): string
    {
        if ($jobs->isEmpty()) return '<p>暂无新职位</p>';
        
        $html = '<ul style="list-style: none; padding: 0;">';
        foreach ($jobs as $job) {
            $html .= '<li style="margin-bottom: 15px; padding: 12px; background: rgba(16, 185, 129, 0.1); border-radius: 8px;">';
            $html .= '<strong style="color: #10b981;">' . e($job->title) . '</strong><br>';
            $html .= '<span style="font-size: 13px; color: #94a3b8;">' . e($job->company_name) . ' | ' . e($job->salary) . ' | ' . e($job->city) . '</span>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function sendTestEmail(): void
    {
        $emails = !empty($this->recipient) ? [$this->recipient] : $this->recipients;
        
        if (empty($emails)) {
            Notification::make()
                ->title('⚠️ 请先添加收件人')
                ->warning()
                ->send();
            return;
        }
        
        $success = 0;
        $failed = 0;
        
        foreach ($emails as $email) {
            try {
                $subject = '🧪 邮件测试 - AI 副业情报局';
                $content = "这是一封测试邮件。\n\n发送时间：" . now()->format('Y-m-d H:i:s') . "\n接收邮箱：{$email}";
                
                $emailLog = EmailLog::create([
                    'recipient' => $email,
                    'subject' => $subject,
                    'content' => $content,
                    'type' => 'test',
                    'status' => 'pending',
                ]);
                
                Mail::raw($content, function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
                
                $emailLog->update(['status' => 'sent', 'sent_at' => now()]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
            }
        }
        
        if ($success > 0 && $failed === 0) {
            Notification::make()
                ->title('✅ 邮件已发送')
                ->body("成功发送 {$success} 封测试邮件")
                ->success()
                ->send();
        }
    }

    private function sendTestEmailToSingle(string $email): void
    {
        try {
            $subject = '🧪 邮件测试 - AI 副业情报局';
            $content = "这是一封测试邮件。\n\n发送时间：" . now()->format('Y-m-d H:i:s');
            
            $emailLog = EmailLog::create([
                'recipient' => $email,
                'subject' => $subject,
                'content' => $content,
                'type' => 'test',
                'status' => 'pending',
            ]);
            
            Mail::raw($content, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
            
            $emailLog->update(['status' => 'sent', 'sent_at' => now()]);
            
            Notification::make()
                ->title('✅ 邮件已发送')
                ->body("测试邮件已发送至：{$email}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ 发送失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getRecentLogsProperty()
    {
        return EmailLog::latest()->limit(10)->get();
    }

    public function bulkImport(): void
    {
        if (empty(trim($this->bulkEmails ?? ''))) {
            Notification::make()
                ->title('请输入邮箱地址')
                ->body('每行一个邮箱地址')
                ->warning()
                ->send();
            return;
        }

        $lines = explode("\n", $this->bulkEmails);
        $added = 0;
        $skipped = 0;
        $invalid = 0;

        foreach ($lines as $line) {
            $email = trim($line);
            
            if (empty($email)) continue;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalid++;
                continue;
            }

            if (in_array($email, $this->recipients)) {
                $skipped++;
                continue;
            }

            $this->recipients[] = $email;
            $added++;
            
            EmailSubscription::firstOrCreate(
                ['email' => $email],
                [
                    'subscribed_to_daily' => true,
                    'subscribed_to_weekly' => true,
                    'subscribed_to_notifications' => true,
                    'unsubscribe_token' => \Illuminate\Support\Str::random(32),
                ]
            );
        }

        EmailSetting::set('email_recipients', json_encode($this->recipients), '邮件接收人列表');
        $this->bulkEmails = '';

        $message = "成功添加 {$added} 个邮箱";
        if ($skipped > 0) $message .= "，跳过 {$skipped} 个重复";
        if ($invalid > 0) $message .= "，{$invalid} 个格式无效";

        Notification::make()
            ->title('批量导入完成')
            ->body($message)
            ->success()
            ->send();
    }

    public function exportRecipients(): void
    {
        $content = implode("\n", $this->recipients);
        
        response()
            ->streamDownload(function () use ($content) {
                echo $content;
            }, 'email-recipients-' . now()->format('Y-m-d') . '.txt', [
                'Content-Type' => 'text/plain',
            ]);
    }

    public function toggleBulkSelect(string $email): void
    {
        if (in_array($email, $this->selectedForBulk)) {
            $this->selectedForBulk = array_values(array_filter($this->selectedForBulk, fn($e) => $e !== $email));
        } else {
            $this->selectedForBulk[] = $email;
        }
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedForBulk)) {
            Notification::make()
                ->title('请先选择要删除的邮箱')
                ->warning()
                ->send();
            return;
        }

        $this->recipients = array_values(array_filter($this->recipients, fn($e) => !in_array($e, $this->selectedForBulk)));
        EmailSetting::set('email_recipients', json_encode($this->recipients), '邮件接收人列表');
        $this->recipients = EmailSetting::getRecipients();
        
        $count = count($this->selectedForBulk);
        $this->selectedForBulk = [];
        
        Notification::make()
            ->title("✅ 已删除 {$count} 个邮箱")
            ->success()
            ->send();
    }

    public function getAvailableTemplatesProperty(): array
    {
        return EmailTemplate::where('is_active', true)
            ->get()
            ->map(fn($t) => [
                'key' => $t->key,
                'name' => $t->name,
                'subject' => $t->subject,
            ])
            ->toArray();
    }
}
