<?php

declare(strict_types=1);

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

    public function __construct(
        private readonly ProvisionRoleProfileAction $provisionProfile,
    ) {}

    public function execute(User $user, string $role): User
    {
        if (! in_array($role, self::SELF_SELECTABLE_ROLES)) {
            throw new \InvalidArgumentException('Invalid role selection.');
        }

        if ($user->role_locked) {
            throw new \InvalidArgumentException('Role is already locked.');
        }

        $roleEnum = UserRole::from($role);

        $user->update([
            'role' => $roleEnum,
            'role_locked' => false,
        ]);

        $user->refresh();
        $this->provisionProfile->execute($user, $roleEnum);

        return $user->fresh(['profile']);
    }
}
