<?php

namespace App\Filament\Resources\BoqRequestResource\Pages;

use App\Filament\Resources\BoqRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBoqRequests extends ListRecords
{
    protected static string $resource = BoqRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
