<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Project;
use App\Models\Article;
use App\Models\EmailLog;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.analytics';
    protected static ?string $navigationLabel = '数据报表';
    protected static ?string $navigationGroup = '运营与数据';
    protected static ?int $navigationSort = 10;

    public function getStats(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::today()->subDays(7);
        $thisMonth = Carbon::today()->subDays(30);

        return [
            'users' => [
                'total' => User::count(),
                'today' => User::whereDate('created_at', $today)->count(),
                'week' => User::whereDate('created_at', '>=', $thisWeek)->count(),
                'month' => User::whereDate('created_at', '>=', $thisMonth)->count(),
            ],
            'vips' => [
                'total' => User::where('role', 'vip')->count(),
                'expiring_soon' => User::where('role', 'vip')
                    ->whereBetween('subscription_ends_at', [now(), now()->addDays(7)])
                    ->count(),
            ],
            'projects' => [
                'total' => Project::count(),
                'this_week' => Project::whereDate('created_at', '>=', $thisWeek)->count(),
            ],
            'emails' => [
                'total_sent' => EmailLog::where('status', 'sent')->count(),
                'today' => EmailLog::where('status', 'sent')->whereDate('created_at', $today)->count(),
                'failed' => EmailLog::where('status', 'failed')->count(),
            ],
        ];
    }
}
