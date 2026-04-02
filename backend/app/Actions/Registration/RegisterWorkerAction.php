<?php

namespace App\Actions\Registration;

use App\Models\User;
use App\Models\Worker;

class RegisterWorkerAction
{
    public function execute(User $user, array $data): Worker
    {
        $worker = Worker::create([
            'user_id' => $user->id,
            'full_name' => $data['full_name'],
            'id_number' => $data['id_number'] ?? null,
            'country_of_origin' => $data['country_of_origin'] ?? null,
            'languages' => $data['languages'] ?? [],
            'date_of_arrival' => $data['date_of_arrival'] ?? null,
            'primary_skill' => $data['primary_skill'] ?? null,
            'secondary_skill' => $data['secondary_skill'] ?? null,
            'previous_experience' => $data['previous_experience'] ?? null,
            'preferred_work_area' => $data['preferred_work_area'] ?? null,
            'available_daily' => $data['available_daily'] ?? false,
            'available_contract' => $data['available_contract'] ?? false,
            'staffing_agency_id' => $data['staffing_agency_id'] ?? null,
            'status' => 'available',
            'eligible_for_assignment' => true,
        ]);

        $user->update([
            'profileable_type' => Worker::class,
            'profileable_id' => $worker->id,
            'role_locked' => true,
            'is_active' => true,
        ]);

        return $worker;
    }
}
