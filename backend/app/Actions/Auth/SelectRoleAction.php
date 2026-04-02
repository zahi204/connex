<?php

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\User;

class SelectRoleAction
{
    private const SELF_SELECTABLE_ROLES = [
        'worker',
        'developer',
        'subcontractor',
        'agency',
    ];

    public function execute(User $user, string $role): User
    {
        if (! in_array($role, self::SELF_SELECTABLE_ROLES)) {
            throw new \InvalidArgumentException('Invalid role selection.');
        }

        if ($user->role_locked) {
            throw new \InvalidArgumentException('Role is already locked.');
        }

        $user->update([
            'role' => UserRole::from($role),
            'role_locked' => false, // locked after wizard completion
        ]);

        return $user->fresh();
    }
}
