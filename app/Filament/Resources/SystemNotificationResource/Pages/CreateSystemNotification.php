<?php

namespace App\Filament\Resources\SystemNotificationResource\Pages;

use App\Filament\Resources\SystemNotificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSystemNotification extends CreateRecord
{
    protected static string $resource = SystemNotificationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'admin_notice';
        $data['is_from_admin'] = true;

        return $data;
    }
}
