<?php

namespace App\Filament\Resources\StaffingAgencyResource\Pages;

use App\Filament\Resources\StaffingAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffingAgency extends EditRecord
{
    protected static string $resource = StaffingAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
