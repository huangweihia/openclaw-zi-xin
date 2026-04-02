<?php

namespace App\Filament\Resources\ContentSubmissionResource\Pages;

use App\Filament\Resources\ContentSubmissionResource;
use App\Services\SubmissionPublisher;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditContentSubmission extends EditRecord
{
    protected static string $resource = ContentSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        if (in_array($record->status, ['approved', 'rejected'], true)) {
            $record->reviewed_by = auth()->id();
            $record->reviewed_at = now();
            if ($record->status === 'approved' && ! $record->published_at) {
                $record->published_at = now();
            }
            $record->saveQuietly();
        }

        if ($record->status !== 'approved') {
            return;
        }

        try {
            SubmissionPublisher::publish($record->fresh());
        } catch (Throwable $e) {
            report($e);
            Notification::make()
                ->danger()
                ->title('投稿已通过，但同步到前台内容表失败')
                ->body($e->getMessage())
                ->persistent()
                ->send();
        }
    }
}
