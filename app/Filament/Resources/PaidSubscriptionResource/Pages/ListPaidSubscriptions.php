<?php

namespace App\Filament\Resources\PaidSubscriptionResource\Pages;

use App\Filament\Resources\PaidSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaidSubscriptions extends ListRecords
{
    protected static string $resource = PaidSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
