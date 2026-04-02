<?php

namespace App\Jobs;

use App\Services\ArticleFetchService;
use App\Services\AISideProjectService;
use App\Models\Category;
use App\Models\Project;
use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;

class FetchContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type;
    public $userId;

    public function __construct(string $type, ?int $userId = null)
    {
        $this->type = $type;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        if ($this->type === 'articles') {
            $this->fetchArticles();
        } elseif ($this->type === 'projects') {
            $this->fetchProjects();
        }
    }

    private function fetchArticles(): void
    {
        try {
            $service = new ArticleFetchService();
            $count = $service->fetchAll();
            
            if ($this->userId) {
                Notification::make()
                    ->title('✅ 文章采集完成')
                    ->body("成功采集 {$count} 篇文章")
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            if ($this->userId) {
                Notification::make()
                    ->title('❌ 文章采集失败')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        }
    }

    private function fetchProjects(): void
    {
        try {
            $service = new AISideProjectService();
            $digest = $service->getDailyDigest();
            
            $category = Category::firstOrCreate(
                ['slug' => 'ai-tools'],
                ['name' => 'AI 工具']
            );
            
            $saved = 0;
            foreach ($digest['hot_projects'] as $projectData) {
                $exists = Project::where('name', $projectData['name'])
                    ->orWhere('url', $projectData['url'])
                    ->exists();
                
                if ($exists) continue;
                
                Project::create([
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
            }
            
            if ($this->userId) {
                Notification::make()
                    ->title('✅ GitHub 项目采集完成')
                    ->body("成功采集 {$saved} 个项目")
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            if ($this->userId) {
                Notification::make()
                    ->title('❌ GitHub 项目采集失败')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        }
    }
}
