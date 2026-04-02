<?php

namespace App\Filament\Pages;

use App\Services\OpenClawGatewayFetcher;
use App\Services\MockAiFetcher;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class AiFetcher extends Page
{
    /** 暂时隐藏采集入口（与 AiAutoFetcher 二选一时可保留其一） */
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static string $view = 'filament.pages.ai-fetcher';
    protected static ?string $navigationLabel = 'AI 自动采集';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationGroup = '内容管理';

    public string $selectedType = 'articles';
    public string $topic = '';
    public int $limit = 5;
    public bool $isProcessing = false;
    public string $status = '';
    public int $resultCount = 0;

    protected function getViewData(): array
    {
        return [
            'selectedType' => $this->selectedType,
            'topic' => $this->topic,
            'limit' => $this->limit,
            'isProcessing' => $this->isProcessing,
            'status' => $this->status,
            'resultCount' => $this->resultCount,
        ];
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
        $this->status = '🔄 正在采集...';
        $this->resultCount = 0;

        try {
            // 使用 OpenClaw Gateway 调用 AI
            $fetcher = new OpenClawGatewayFetcher();
            
            switch ($this->selectedType) {
                case 'articles':
                    $count = $fetcher->fetchArticles($this->topic, $this->limit);
                    $this->resultCount = $count;
                    $this->status = "✅ 成功采集 {$count} 篇文章";
                    break;
                    
                case 'projects':
                    $count = $fetcher->fetchProjects($this->topic, $this->limit);
                    $this->resultCount = $count;
                    $this->status = "✅ 成功采集 {$count} 个项目";
                    break;
                    
                case 'jobs':
                    $count = $fetcher->fetchJobs($this->topic, $this->limit);
                    $this->resultCount = $count;
                    $this->status = "✅ 成功采集 {$count} 个职位";
                    break;
                    
                case 'knowledge':
                    $kb = \App\Models\KnowledgeBase::firstOrCreate(
                        ['title' => 'AI 技术教程'],
                        [
                            'user_id' => 1,
                            'description' => 'AI 自动生成的技术文档',
                            'category' => 'tech',
                            'is_public' => true,
                        ]
                    );
                    $count = $fetcher->fetchKnowledge($this->topic, $kb->id);
                    $this->resultCount = $count;
                    $this->status = "✅ 成功生成 {$count} 篇知识库文档";
                    break;
            }

            Notification::make()
                ->title('✅ 采集完成')
                ->body($this->status)
                ->success()
                ->send();

        } catch (\Exception $e) {
            $this->status = "❌ 采集失败：" . $e->getMessage();
            
            Notification::make()
                ->title('❌ 采集失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isProcessing = false;
        }
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
