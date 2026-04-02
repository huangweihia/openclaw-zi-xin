<?php

namespace App\Filament\Resources\ContentSubmissionResource\Pages;

use App\Filament\Resources\ContentSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListContentSubmissions extends ListRecords
{
    protected static string $resource = ContentSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
