<?php

namespace App\Filament\Resources\TrainingCycleResource\Pages;

use App\Filament\Resources\TrainingCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainingCycles extends ListRecords
{
    protected static string $resource = TrainingCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
