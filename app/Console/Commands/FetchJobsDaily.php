<?php

namespace App\Console\Commands;

use App\Services\BossJobService;
use Illuminate\Console\Command;

class FetchJobsDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:fetch-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每日自动采集 BOSS 直聘 AI 职位';

    /**
     * Execute the console command.
     */
    public function handle(BossJobService $service): int
    {
        $this->info('💼 开始执行每日职位采集任务...');
        
        $count = $service->fetchAll();
        
        $this->info("✅ 任务完成，共采集 {$count} 个职位");
        
        return Command::SUCCESS;
    }
}
