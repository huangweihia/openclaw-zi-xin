<?php

namespace App\Jobs;

use App\Models\AsyncTask;
use App\Services\ArticleFetchService;
use App\Services\AISideProjectService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;

class ProcessAsyncTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public AsyncTask $task;

    public function __construct(AsyncTask $task)
    {
        $this->task = $task;
    }

    public function handle(): void
    {
        $this->task->start();

        try {
            switch ($this->task->type) {
                case 'articles':
                case 'fetch_articles':
                case '文章':
                    $this->fetchArticles();
                    break;
                    
                case 'projects':
                case 'fetch_projects':
                case '项目':
                    $this->fetchProjects();
                    break;
                    
                case 'jobs':
                case 'fetch_jobs':
                case '职位':
                    $this->fetchJobs();
                    break;
                    
                case 'knowledge':
                case 'knowledge_fetch':
                case '知识库':
                    $this->fetchKnowledge();
                    break;
                    
                default:
                    throw new \Exception("未知任务类型：{$this->task->type}");
            }

            $this->task->complete(
                $this->task->success ?? $this->task->processed,
                $this->task->failed ?? 0
            );

            $userId = $this->task->meta['user_id'] ?? null;
            if ($userId) {
                Notification::make()
                    ->title("✅ {$this->task->name} 完成")
                    ->body("成功：{$this->task->success} | 失败：{$this->task->failed}")
                    ->success()
                    ->send();
            }

        } catch (\Exception $e) {
            $this->task->fail($e->getMessage());

            $userId = $this->task->meta['user_id'] ?? null;
            if ($userId) {
                Notification::make()
                    ->title("❌ {$this->task->name} 失败")
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }

            throw $e;
        }
    }

    private function fetchArticles(): void
    {
        $service = new ArticleFetchService();
        $count = $service->fetchAll();
        $this->task->updateProgress($count, $count, 0);
    }

    private function fetchProjects(): void
    {
        $service = new AISideProjectService();
        $digest = $service->getDailyDigest();
        
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'ai-tools'],
            ['name' => 'AI 工具']
        );
        
        $saved = 0;
        $failed = 0;
        
        foreach ($digest['hot_projects'] as $projectData) {
            try {
                $exists = \App\Models\Project::where('name', $projectData['name'])
                    ->orWhere('url', $projectData['url'])
                    ->exists();
                
                if ($exists) continue;
                
                \App\Models\Project::create([
                    'name' => $projectData['name'],
                    'full_name' => $projectData['name'],
                    'description' => $projectData['description'] ?? '暂无描述',
                    'url' => $projectData['url'],
                    'language' => $projectData['language'] ?? null,
                    'stars' => (int) ($projectData['stars'] ?? 0),
                    'forks' => (int) ($projectData['forks'] ?? 0),
                    'category_id' => $category->id,
                    'tags' => $projectData['tags'] ?? [],
                    'monetization' => 'medium',
                    'difficulty' => 'medium',
                    'is_featured' => ($projectData['stars'] ?? 0) > 5000,
                    'collected_at' => now(),
                ]);
                
                $saved++;
            } catch (\Exception $e) {
                $failed++;
            }
        }
        
        $this->task->updateProgress($saved + $failed, $saved, $failed);
    }

    private function fetchJobs(): void
    {
        $this->task->updateProgress(0, 0, 0);
    }

    private function fetchKnowledge(): void
    {
        $this->task->updateProgress(0, 0, 0);
    }
}