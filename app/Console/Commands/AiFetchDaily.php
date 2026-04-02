<?php

namespace App\Console\Commands;

use App\Services\OpenClawGatewayFetcher;
use App\Services\MockAiFetcher;
use App\Models\KnowledgeBase;
use Illuminate\Console\Command;

class AiFetchDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:fetch-daily {--topic= : 自定义主题} {--mock : 使用模拟数据} {--gateway : 使用 OpenClaw Gateway}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每日使用 AI 自动获取 AI 相关内容（文章/项目/职位/知识库）';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $useMock = $this->option('mock');
        $useGateway = $this->option('gateway');
        
        if ($useGateway) {
            $fetcher = new OpenClawGatewayFetcher();
            $this->info('🤖 使用 OpenClaw Gateway 模式（真实 AI 生成）');
        } elseif ($useMock) {
            $fetcher = new MockAiFetcher();
            $this->info('🧪 使用模拟数据模式（开发测试）');
        } else {
            // 默认使用 Gateway 模式
            $fetcher = new OpenClawGatewayFetcher();
            $this->info('🤖 使用 OpenClaw Gateway 模式（真实 AI 生成）');
        }
        
        $this->info('════════════════════════════════════════');
        
        $startTime = now();
        
        // 1. 获取 AI 文章
        $this->newLine();
        $this->info('📝 [1/4] 获取 AI 文章...');
        $articleTopic = $this->option('topic') ?: 'AI 大模型 最新动态 GPT-5';
        $articles = $fetcher->fetchArticles($articleTopic, 5);
        $this->info("✅ 获取 {$articles} 篇文章");
        
        // 2. 获取 GitHub 项目
        $this->newLine();
        $this->info('💻 [2/4] 获取 GitHub AI 项目...');
        $projectTopic = $this->option('topic') ?: 'machine-learning artificial-intelligence gpt llm';
        $projects = $fetcher->fetchProjects($projectTopic, 10);
        $this->info("✅ 获取 {$projects} 个项目");
        
        // 3. 获取 AI 职位
        $this->newLine();
        $this->info('💼 [3/4] 获取 AI 职位...');
        $jobTopic = $this->option('topic') ?: 'AI 工程师 大模型 AIGC 算法工程师';
        $jobs = $fetcher->fetchJobs($jobTopic, 10);
        $this->info("✅ 获取 {$jobs} 个职位");
        
        // 4. 生成知识库文档
        $this->newLine();
        $this->info('📚 [4/4] 生成知识库文档...');
        $knowledgeTopic = $this->option('topic') ?: 'ChatGPT 使用技巧';
        
        $adminUser = \App\Models\User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = \App\Models\User::first();
        }
        $userId = $adminUser ? $adminUser->id : 1;
        
        $knowledgeBase = KnowledgeBase::firstOrCreate(
            ['title' => 'AI 技术教程'],
            [
                'user_id' => $userId,
                'description' => 'AI 自动生成的技术文档',
                'category' => 'tech',
                'is_public' => true,
                'is_vip_only' => false,
            ]
        );
        
        $knowledge = $fetcher->fetchKnowledge($knowledgeTopic, $knowledgeBase->id);
        $this->info("✅ 获取 {$knowledge} 篇知识库文档");
        
        // 完成统计
        $this->newLine();
        $this->info('════════════════════════════════════════');
        $duration = now()->diffInSeconds($startTime);
        $this->info("✅ AI 自动获取任务完成！");
        $this->info("⏱️  耗时：{$duration} 秒");
        $this->info("📊 统计：文章 {$articles} 篇 | 项目 {$projects} 个 | 职位 {$jobs} 个 | 知识库 {$knowledge} 篇");
        $this->info('════════════════════════════════════════');
        
        return Command::SUCCESS;
    }
}
