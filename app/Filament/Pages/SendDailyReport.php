<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Providers\Filament\AdminPanelProvider;

class SendDailyReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-ennelope';
    protected static string $view = 'filament.pages.send-daily-report';
    protected static ?string $navigationLabel = '日报发送（测试）';
    protected static ?int $navigationSort = 99;
    protected static bool $shouldRegisterNavigation = false;
    
    public function mount(): void
    {
        $this->abort(500, 'Do not access directly');
    }
}
