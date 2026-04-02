<?php

namespace App\Filament\Widgets;

use App\Models\EmailLog;
use Filament\Widgets\ChartWidget;

class EmailSentChartWidget extends ChartWidget
{
    protected static ?string $heading = '邮件发送趋势';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;
    
    protected function getData(): array
    {
        $days = 7;
        $data = [];
        $labels = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('m-d');
            
            $count = EmailLog::whereDate('created_at', $date)
                ->where('status', 'sent')
                ->count();
            
            $data[] = $count;
            $labels[] = $dateStr;
        }
        
        return [
            'datasets' => [
                [
                    'label' => '发送数量',
                    'data' => $data,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.5)',
                    'borderColor' => 'rgba(99, 102, 241, 1)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
