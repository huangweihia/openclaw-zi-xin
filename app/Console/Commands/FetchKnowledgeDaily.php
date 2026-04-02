<?php

namespace App\Console\Commands;

use App\Services\KnowledgeFetchService;
use App\Models\AsyncTask;
use Illuminate\Console\Command;

class FetchKnowledgeDaily extends Command
{
    protected $signature = 'knowledge:fetch-daily';
    protected $description = '每日自动采集 AI 相关知识库';

    public function handle(KnowledgeFetchService $service): int
    {
        $this->info('📚 开始执行知识库采集任务...');
        
        // 创建任务记录
        $task = AsyncTask::createTask('知识库采集', 'knowledge_fetch');
        $task->start();
        
        try {
            $count = $service->fetchAll();
            $task->complete($count, 0);
            
            $this->info("✅ 任务完成，共采集 {$count} 篇知识库文章");
        } catch (\Exception $e) {
            $task->fail($e->getMessage());
            $this->error("❌ 任务失败：" . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
