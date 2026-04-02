<?php

namespace App\Filament\Pages;

use App\Models\AsyncTask;
use App\Services\ArticleFetchService;
use App\Services\AISideProjectService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class TaskManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string $view = 'filament.pages.task-manager';
    protected static ?string $navigationLabel = '任务管理';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationGroup = 'AI 采集';
    // 避免与后台入口菜单重叠：按需求隐藏任务管理导航
    protected static bool $shouldRegisterNavigation = false;
    
    public function getTasksProperty()
    {
        return AsyncTask::query()
            ->latest()
            ->limit(50)
            ->get();
    }
    
    public static function getNavigationBadge(): ?string
    {
        $count = AsyncTask::where('status', 'running')->count();
        return $count > 0 ? (string) $count : null;
    }

    /**
     * 采集文章
     */
    public function fetchArticles(): void
    {
        $task = AsyncTask::createTask('文章采集', 'fetch_articles');
        
        try {
            $task->start();
            
            $service = new ArticleFetchService();
            $count = $service->fetchAll();
            
            $task->complete($count, 0);
            
            Notification::make()
                ->title('✅ 文章采集完成')
                ->body("成功采集 {$count} 篇不重复文章")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            $task->fail($e->getMessage());
            
            Notification::make()
                ->title('❌ 文章采集失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * 采集项目
     */
    public function fetchProjects(): void
    {
        $task = AsyncTask::createTask('项目采集', 'fetch_projects');
        
        try {
            $task->start();
            
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
            
            $task->complete($saved, $failed);
            
            Notification::make()
                ->title('✅ 项目采集完成')
                ->body("成功：{$saved} | 失败：{$failed}")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            $task->fail($e->getMessage());
            
            Notification::make()
                ->title('❌ 项目采集失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
