<?php

namespace App\Filament\Resources\BoqRequestResource\Pages;

use App\Filament\Resources\BoqRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoqRequest extends EditRecord
{
    protected static string $resource = BoqRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
