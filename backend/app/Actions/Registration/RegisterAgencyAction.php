<?php

namespace App\Actions\Registration;

use App\Models\StaffingAgency;
use App\Models\User;

class RegisterAgencyAction
{
    public function execute(User $user, array $data): StaffingAgency
    {
        $agency = StaffingAgency::create([
            'user_id' => $user->id,
            'agency_name' => $data['agency_name'],
            'registration_number' => $data['registration_number'] ?? null,
            'email' => $data['email'] ?? null,
            'contact_person_name' => $data['contact_person_name'] ?? null,
            'contact_person_role' => $data['contact_person_role'] ?? null,
            'contact_person_phone' => $data['contact_person_phone'] ?? null,
            'contact_person_email' => $data['contact_person_email'] ?? null,
            'worker_types' => $data['worker_types'] ?? [],
            'countries_of_origin' => $data['countries_of_origin'] ?? [],
            'average_capacity' => $data['average_capacity'] ?? null,
            'monthly_throughput' => $data['monthly_throughput'] ?? null,
        ]);

        $user->update([
            'profileable_type' => StaffingAgency::class,
            'profileable_id' => $agency->id,
            'role_locked' => true,
            'is_active' => true,
        ]);

        return $agency;
    }
}
