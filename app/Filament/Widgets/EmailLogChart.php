<?php

namespace App\Filament\Widgets;

use App\Models\EmailLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class EmailLogChart extends ChartWidget
{
    protected static ?string $heading = '邮件发送统计';
    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        // 获取最近 7 天的数据
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('m-d');
            
            $sent = EmailLog::whereDate('created_at', $date)
                           ->where('status', 'sent')
                           ->count();
            
            $failed = EmailLog::whereDate('created_at', $date)
                             ->where('status', 'failed')
                             ->count();
            
            $data[] = $sent;
        }
        
        return [
            'datasets' => [
                [
                    'label' => '成功发送',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
