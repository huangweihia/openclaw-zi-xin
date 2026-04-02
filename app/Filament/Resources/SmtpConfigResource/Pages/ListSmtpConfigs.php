<?php

namespace App\Filament\Resources\SmtpConfigResource\Pages;

use App\Filament\Resources\SmtpConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSmtpConfigs extends ListRecords
{
    protected static string $resource = SmtpConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 不允许创建，只能编辑现有配置
        ];
    }
}
