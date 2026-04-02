<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Project;
use App\Models\SideHustleCase;
use App\Models\AiToolMonetization;
use App\Services\WechatBotService;
use Illuminate\Console\Command;

class SendWechatDailyDigest extends Command
{
    protected $signature = 'wechat:send-daily-digest';
    protected $description = '发送每日 AI 资讯到企业微信群';

    protected WechatBotService $bot;

    public function __construct(WechatBotService $bot)
    {
        parent::__construct();
        $this->bot = $bot;
    }

    public function handle(): int
    {
        $this->info('📱 开始发送每日 AI 资讯...');

        // 获取今日数据
        $today = now()->startOfDay();
        
        $data = [
            'projects' => Project::whereDate('created_at', $today)
                ->orderBy('stars', 'desc')
                ->limit(10)
                ->get(['name', 'description', 'stars'])
                ->toArray(),
            
            'cases' => SideHustleCase::whereDate('created_at', $today)
                ->orderByDesc('id')
                ->limit(5)
                ->get(['title', 'estimated_monthly_income'])
                ->toArray(),
            
            'tools' => AiToolMonetization::whereDate('created_at', $today)
                ->orderByDesc('id')
                ->limit(5)
                ->get(['tool_name', 'description'])
                ->toArray(),
        ];

        // 如果没有新内容，发送昨日精选
        if (empty($data['projects']) && empty($data['cases']) && empty($data['tools'])) {
            $this->info('⚠️ 今日无新内容，发送精选内容...');
            
            $data = [
                'projects' => Project::orderBy('stars', 'desc')
                    ->limit(5)
                    ->get(['name', 'description', 'stars'])
                    ->toArray(),
                
                'cases' => SideHustleCase::orderByDesc('id')
                    ->limit(3)
                    ->get(['title', 'estimated_monthly_income'])
                    ->toArray(),
                
                'tools' => AiToolMonetization::orderByDesc('popularity_score')
                    ->limit(3)
                    ->get(['tool_name', 'description'])
                    ->toArray(),
            ];
        }

        // 发送消息
        $success = $this->bot->sendDailyDigest($data);

        if ($success) {
            $this->info('✅ 发送成功！');
            return Command::SUCCESS;
        }

        $this->error('❌ 发送失败！');
        return Command::FAILURE;
    }
}
