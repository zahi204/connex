<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Minimal seeder for the staging environment.
 * Creates only the admin + coordinator accounts needed for Filament login.
 *
 * Run with:
 *   php artisan db:seed --class=StagingSeeder --force
 */
class StagingSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('STAGING_ADMIN_EMAIL', 'admin@connex-build.co.il');
        $adminPassword = env('STAGING_ADMIN_PASSWORD', 'ChangeMe!2026');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Staging Admin',
                'phone' => '+972500000001',
                'password' => $adminPassword,
                'role' => UserRole::Admin,
                'role_locked' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $this->command?->info("Staging admin ready: {$adminEmail}");
    }
}
