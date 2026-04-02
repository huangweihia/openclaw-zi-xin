<?php

namespace App\Console\Commands;

use App\Services\AISideProjectService;
use App\Models\Project;
use App\Models\Category;
use Illuminate\Console\Command;

class FetchGitHubProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:fetch-github';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '手动采集 GitHub AI 项目';

    /**
     * Execute the console command.
     */
    public function handle(AISideProjectService $service): int
    {
        $this->info('🚀 开始采集 GitHub AI 项目...');
        
        $digest = $service->getDailyDigest();
        $saved = 0;
        
        // 获取或创建分类
        $aiToolsCategory = Category::firstOrCreate(
            ['slug' => 'ai-tools'],
            ['name' => 'AI 工具']
        );
        
        foreach ($digest['hot_projects'] as $projectData) {
            try {
                // 检查是否已存在
                $exists = Project::where('name', $projectData['name'])
                    ->orWhere('url', $projectData['url'])
                    ->exists();
                
                if ($exists) {
                    $this->info("⏭️ 跳过已存在：{$projectData['name']}");
                    continue;
                }
                
                // 创建项目
                Project::create([
                    'name' => $projectData['name'],
                    'full_name' => $projectData['name'],
                    'description' => $projectData['description'] ?? '暂无描述',
                    'url' => $projectData['url'],
                    'language' => $projectData['language'] ?? null,
                    'stars' => (int) ($projectData['stars'] ?? 0),
                    'forks' => (int) ($projectData['forks'] ?? 0),
                    'category_id' => $aiToolsCategory->id,
                    'tags' => $projectData['tags'] ?? [],
                    'monetization' => 'medium',
                    'difficulty' => 'medium',
                    'is_featured' => ($projectData['stars'] ?? 0) > 5000,
                    'collected_at' => now(),
                ]);
                
                $saved++;
                $this->info("✅ 保存：{$projectData['name']}");
            } catch (\Exception $e) {
                $this->error("❌ 保存失败：{$projectData['name']} - {$e->getMessage()}");
            }
        }
        
        $this->info("✅ 采集完成，共保存 {$saved} 个项目");
        
        return Command::SUCCESS;
    }
}
