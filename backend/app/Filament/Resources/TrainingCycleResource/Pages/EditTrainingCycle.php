<?php

namespace App\Filament\Resources\TrainingCycleResource\Pages;

use App\Filament\Resources\TrainingCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainingCycle extends EditRecord
{
    protected static string $resource = TrainingCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
