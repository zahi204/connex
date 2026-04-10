<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\WorkerResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateWorker extends CreateRecord
{
    protected static string $resource = WorkerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $phone = $data['phone'] ?? null;
        unset($data['phone']);

        if ($phone) {
            $user = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'role' => UserRole::Worker,
                    'is_active' => true,
                ],
            );

            $data['user_id'] = $user->id;
        }

        return $data;
    }
}
