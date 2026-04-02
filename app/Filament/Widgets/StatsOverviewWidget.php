<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Article;
use App\Models\Project;
use App\Models\EmailLog;
use App\Models\EmailSubscription;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();
        
        return [
            Stat::make('总用户数', User::count())
                ->description('今日新增 ' . User::whereDate('created_at', $today)->count() . ' 人')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            
            Stat::make('VIP 用户', User::where('role', 'vip')->orWhere('subscription_ends_at', '>', now())->count())
                ->description('转化率 ' . round(User::where('role', 'vip')->count() / max(1, User::count()) * 100, 2) . '%')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
            
            Stat::make('文章总数', Article::count())
                ->description('今日新增 ' . Article::whereDate('created_at', $today)->count() . ' 篇')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            
            Stat::make('项目总数', Project::count())
                ->description('已收录 ' . Project::where('is_featured', true)->count() . ' 个精选')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
            
            Stat::make('邮件发送', EmailLog::whereDate('created_at', $today)->count())
                ->description('今日发送 ' . EmailLog::whereDate('created_at', $today)->where('status', 'sent')->count() . ' 封')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('success'),
            
            Stat::make('邮件列表订阅', EmailSubscription::whereNull('unsubscribed_at')->count())
                ->description('开通过日报 ' . EmailSubscription::where('subscribed_to_daily', true)->whereNull('unsubscribed_at')->count() . ' 人')
                ->descriptionIcon('heroicon-m-bell')
                ->color('primary'),

            Stat::make('付费会员（有效）', Subscription::query()->active()->count())
                ->description('subscriptions 表 · 后台「付费会员」管理')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),
        ];
    }
}
