<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\Developer;
use App\Models\StaffingAgency;
use App\Models\Subcontractor;
use App\Models\User;
use App\Models\Worker;

/**
 * Creates a minimal profile row when a role is selected so the morph
 * relationship is never null while the user is mid-onboarding.
 * The wizard's Register*Action will updateOrCreate over this placeholder.
 */
class ProvisionRoleProfileAction
{
    public function execute(User $user, UserRole $role): void
    {
        if ($user->profileable_id !== null) {
            return;
        }

        match ($role) {
            UserRole::Worker => $this->provisionWorker($user),
            UserRole::Developer => $this->provisionDeveloper($user),
            UserRole::Subcontractor => $this->provisionSubcontractor($user),
            UserRole::Agency => $this->provisionAgency($user),
            default => null,
        };
    }

    private function provisionWorker(User $user): void
    {
        $worker = Worker::create([
            'user_id' => $user->id,
            'full_name' => $user->phone ?? 'Worker',
            'languages' => [],
            'available_daily' => false,
            'available_contract' => false,
            'status' => 'available',
            'eligible_for_assignment' => false,
        ]);

        $user->update([
            'profileable_type' => Worker::class,
            'profileable_id' => $worker->id,
        ]);
    }

    private function provisionDeveloper(User $user): void
    {
        $developer = Developer::create([
            'user_id' => $user->id,
            'company_name' => ($user->phone ?? 'Company') . ' — pending',
            'areas_of_operation' => [],
            'specializations' => [],
        ]);

        $user->update([
            'profileable_type' => Developer::class,
            'profileable_id' => $developer->id,
        ]);
    }

    private function provisionSubcontractor(User $user): void
    {
        $subcontractor = Subcontractor::create([
            'user_id' => $user->id,
            'name' => ($user->phone ?? 'Subcontractor') . ' — pending',
            'specializations' => [],
            'operating_areas' => [],
            'status' => 'available',
        ]);

        $user->update([
            'profileable_type' => Subcontractor::class,
            'profileable_id' => $subcontractor->id,
        ]);
    }

    private function provisionAgency(User $user): void
    {
        $agency = StaffingAgency::create([
            'user_id' => $user->id,
            'agency_name' => ($user->phone ?? 'Agency') . ' — pending',
            'worker_types' => [],
            'countries_of_origin' => [],
        ]);

        $user->update([
            'profileable_type' => StaffingAgency::class,
            'profileable_id' => $agency->id,
        ]);
    }
}
