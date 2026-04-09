<?php

namespace App\Actions\Registration;

use App\Models\Subcontractor;
use App\Models\User;

class RegisterSubcontractorAction
{
    public function execute(User $user, array $data): Subcontractor
    {
        $subcontractor = Subcontractor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $data['name'],
                'registration_number' => $data['registration_number'] ?? null,
                'email' => $data['email'] ?? null,
                'specializations' => $data['specializations'] ?? [],
                'operating_areas' => $data['operating_areas'] ?? [],
                'number_of_workers' => $data['number_of_workers'] ?? null,
                'years_of_experience' => $data['years_of_experience'] ?? null,
                'notable_projects' => $data['notable_projects'] ?? null,
                'status' => 'available',
            ],
        );

        $user->update([
            'profileable_type' => Subcontractor::class,
            'profileable_id' => $subcontractor->id,
            'role_locked' => true,
            'is_active' => true,
        ]);

        return $subcontractor;
    }
}
