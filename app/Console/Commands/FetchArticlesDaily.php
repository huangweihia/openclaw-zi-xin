<?php

namespace App\Console\Commands;

use App\Services\ArticleFetchService;
use Illuminate\Console\Command;

class FetchArticlesDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每日自动采集文章（公众号/知乎/小红书）';

    /**
     * Execute the console command.
     */
    public function handle(ArticleFetchService $service): int
    {
        $this->info('🚀 开始执行每日文章采集任务...');
        
        $count = $service->fetchAll();
        
        $this->info("✅ 任务完成，共采集 {$count} 篇文章");
        
        return Command::SUCCESS;
    }
}
