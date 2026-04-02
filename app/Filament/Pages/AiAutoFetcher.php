<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class AiAutoFetcher extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static string $view = 'filament.pages.ai-auto-fetcher';
    protected static ?string $navigationLabel = 'AI 自动采集';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationGroup = 'AI 采集';

    public string $selectedType = 'articles';
    public string $topic = '';
    public int $limit = 5;
    public bool $isProcessing = false;
    public string $status = '';
    public int $resultCount = 0;

    public function mount(): void
    {
        // 设置默认值
        $this->topic = match($this->selectedType) {
            'articles' => 'AI 大模型 最新动态',
            'projects' => 'machine-learning gpt llm',
            'jobs' => 'AI 工程师 大模型',
            'knowledge' => 'ChatGPT 使用技巧',
            default => '',
        };
    }

    public function startFetch(): void
    {
        if (empty($this->topic)) {
            Notification::make()
                ->title('❌ 请输入采集主题')
                ->danger()
                ->send();
            return;
        }

        $this->isProcessing = true;
        $this->status = '🔄 正在调用 OpenClaw AI...';
        $this->resultCount = 0;

        try {
            // 调用 OpenClaw AI
            $result = $this->callOpenClawAI($this->selectedType, $this->topic, $this->limit);
            
            if ($result['success']) {
                $this->resultCount = $result['saved'] ?? 0;
                $this->status = "✅ " . ($result['message'] ?? '采集成功');
                
                Notification::make()
                    ->title('✅ 采集完成')
                    ->body($this->status)
                    ->success()
                    ->send();
            } else {
                $this->status = "❌ " . ($result['message'] ?? '采集失败');
                
                Notification::make()
                    ->title('❌ 采集失败')
                    ->body($this->status)
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            $this->status = "❌ 异常：" . $e->getMessage();
            
            Notification::make()
                ->title('❌ 采集失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isProcessing = false;
        }
    }

    protected function callOpenClawAI(string $type, string $topic, int $limit): array
    {
        // 这里调用 OpenClaw API 或者直接生成
        // 为了演示，我们直接调用之前创建的 Webhook API
        
        $webhookUrl = $this->getOpenClawWebhookUrl();
        $token = env('OPENCLAW_WEBHOOK_TOKEN', 'openclaw-ai-fetcher-2026');
        
        // 构造请求数据
        $requestData = $this->buildRequestData($type, $topic, $limit);
        
        // 发送 HTTP 请求
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-API-Token' => $token,
        ])->post($webhookUrl, $requestData);
        
        if ($response->successful()) {
            return $response->json();
        }

        $body = $response->json();
        $msg = is_array($body) && isset($body['message'])
            ? (string) $body['message']
            : $response->body();

        return [
            'success' => false,
            'message' => 'API 调用失败（HTTP ' . $response->status() . '）：' . $msg,
        ];
    }

    /**
     * 后台自调 Webhook：用 internal_url（容器内通常为 80），勿用 APP_URL 的宿主机端口。
     */
    protected function getOpenClawWebhookUrl(): string
    {
        $base = rtrim((string) config('app.internal_url', ''), '/');

        return $base !== ''
            ? $base . '/api/openclaw/webhook'
            : url('/api/openclaw/webhook');
    }

    protected function buildRequestData(string $type, string $topic, int $limit): array
    {
        // 这里可以调用 OpenClaw 生成真实数据
        // 为了演示，我们返回模拟数据
        
        return match($type) {
            'articles' => [
                'type' => 'articles',
                'items' => [
                    [
                        'title' => "{$topic} - 最新文章",
                        'summary' => "关于{$topic}的最新报道",
                        'content' => "详细内容...",
                        'url' => 'https://example.com/article-' . time(),
                    ],
                ],
            ],
            'projects' => [
                'type' => 'projects',
                'items' => [
                    [
                        'name' => 'test-project-' . time(),
                        'description' => "关于{$topic}的项目",
                        'url' => 'https://github.com/test/project-' . time(),
                        'stars' => 1000,
                        'forks' => 100,
                        'language' => 'Python',
                    ],
                ],
            ],
            'jobs' => [
                'type' => 'jobs',
                'items' => [
                    [
                        'title' => "{$topic}工程师",
                        'company_name' => '某某科技',
                        'salary' => '30-60K',
                        'city' => '北京',
                        'experience' => '3-5 年',
                        'education' => '本科',
                        'description' => "职位描述...",
                        'url' => 'https://example.com/job-' . time(),
                        'tags' => [$topic, 'AI'],
                    ],
                ],
            ],
            'knowledge' => [
                'type' => 'knowledge',
                'items' => [
                    [
                        'title' => $topic,
                        'content' => "<h1>{$topic}</h1><p>详细内容...</p>",
                    ],
                ],
            ],
            default => [],
        };
    }

    public function quickFetch(string $type): void
    {
        $topics = [
            'articles' => 'AI 大模型 最新动态',
            'projects' => 'machine-learning gpt llm',
            'jobs' => 'AI 工程师 大模型 AIGC',
            'knowledge' => 'ChatGPT 使用技巧',
        ];

        $this->selectedType = $type;
        $this->topic = $topics[$type] ?? '';
        $this->limit = $type === 'articles' ? 5 : 10;
        
        $this->startFetch();
    }
}
