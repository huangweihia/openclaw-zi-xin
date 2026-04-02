<?php

namespace App\Filament\Resources\ProblemFeedbackResource\Pages;

use App\Filament\Resources\ProblemFeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProblemFeedback extends EditRecord
{
    protected static string $resource = ProblemFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

