<?php

namespace App\Filament\Resources\JobListingResource\Pages;

use App\Filament\Resources\JobListingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJobListing extends CreateRecord
{
    protected static string $resource = JobListingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }
        if (! array_key_exists('is_published', $data) || $data['is_published'] === null) {
            $data['is_published'] = true;
        }
        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
