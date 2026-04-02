<?php

namespace App\Actions\Registration;

use App\Models\Developer;
use App\Models\User;

class RegisterDeveloperAction
{
    public function execute(User $user, array $data): Developer
    {
        $developer = Developer::create([
            'user_id' => $user->id,
            'company_name' => $data['company_name'],
            'registration_number' => $data['registration_number'] ?? null,
            'email' => $data['email'] ?? null,
            'company_description' => $data['company_description'] ?? null,
            'company_size' => $data['company_size'] ?? null,
            'areas_of_operation' => $data['areas_of_operation'] ?? [],
            'specializations' => $data['specializations'] ?? [],
            'contact_person_name' => $data['contact_person_name'] ?? null,
            'contact_person_role' => $data['contact_person_role'] ?? null,
            'contact_person_phone' => $data['contact_person_phone'] ?? null,
            'contact_person_email' => $data['contact_person_email'] ?? null,
        ]);

        $user->update([
            'profileable_type' => Developer::class,
            'profileable_id' => $developer->id,
            'role_locked' => true,
            'is_active' => true,
        ]);

        return $developer;
    }
}
