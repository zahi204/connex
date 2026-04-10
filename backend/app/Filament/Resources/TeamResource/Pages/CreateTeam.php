<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\TeamResource;
use App\Models\User;
use App\Models\Worker;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $phone = $data['leader_phone'] ?? null;
        unset($data['leader_phone']);

        if ($phone) {
            $user = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'role' => UserRole::Worker,
                    'is_active' => true,
                ],
            );

            $worker = Worker::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => $phone,
                    'status' => 'available',
                ],
            );

            $data['team_leader_id'] = $worker->id;
        }

        return $data;
    }
}
