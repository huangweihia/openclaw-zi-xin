<?php

namespace App\Filament\Resources\AdSlotResource\Pages;

use App\Filament\Resources\AdSlotResource;
use App\Support\PublicStorageFallback;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditAdSlot extends EditRecord
{
    protected static string $resource = AdSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $raw = $data['image_path'] ?? null;
        $path = is_array($raw) ? (Arr::first(array_filter($raw)) ?: null) : $raw;
        if (is_string($path) && $path !== '') {
            PublicStorageFallback::ensurePublicWebCopyFromStorageLegacy($path);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $path = $this->record->image_path;
        if (filled($path)) {
            PublicStorageFallback::ensurePublicWebCopyFromStorageLegacy($path);
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $clearImage = (bool) ($data['clear_image'] ?? false);
        unset($data['clear_image']);

        $path = $data['image_path'] ?? null;
        if (is_array($path)) {
            $path = Arr::first(array_filter($path)) ?: null;
            $data['image_path'] = $path;
        }
        $url = isset($data['image_url']) ? trim((string) $data['image_url']) : '';
        $data['image_url'] = $url !== '' ? $url : null;

        if ($clearImage) {
            if ($this->record->image_path) {
                PublicStorageFallback::deleteFromBothDisks($this->record->image_path);
            }
            $data['image_path'] = null;
            $data['image_url'] = null;

            return $data;
        }

        if (filled($path)) {
            $data['image_url'] = null;
            if ($this->record->image_path && $this->record->image_path !== $path) {
                PublicStorageFallback::deleteFromBothDisks($this->record->image_path);
            }
        } elseif (filled($data['image_url'])) {
            if ($this->record->image_path) {
                PublicStorageFallback::deleteFromBothDisks($this->record->image_path);
            }
            $data['image_path'] = null;
        }

        return $data;
    }
}
