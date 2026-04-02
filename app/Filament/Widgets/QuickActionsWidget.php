<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static ?string $heading = '快捷操作';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 1;
    
    protected static string $view = 'filament.widgets.quick-actions-widget';
}
