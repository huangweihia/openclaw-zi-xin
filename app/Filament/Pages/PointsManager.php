<?php

namespace App\Filament\Pages;

use App\Models\UserPoint;
use App\Models\PointTransaction;
use Filament\Pages\Page;

class PointsManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static string $view = 'filament.pages.points-manager';
    protected static ?string $navigationLabel = '积分管理';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationGroup = '用户与互动';

    public function getTransactionsProperty()
    {
        return PointTransaction::with('user')
            ->latest()
            ->paginate(20);
    }

    public function getStatsProperty(): array
    {
        return [
            'total_earned' => PointTransaction::where('amount', '>', 0)->sum('amount'),
            'total_spent' => PointTransaction::where('amount', '<', 0)->sum('amount') * -1,
            'users_with_points' => UserPoint::where('balance', '>', 0)->count(),
            'today_transactions' => PointTransaction::whereDate('created_at', today())->count(),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = PointTransaction::whereDate('created_at', today())->count();
        return $count > 0 ? (string) $count : null;
    }
}
