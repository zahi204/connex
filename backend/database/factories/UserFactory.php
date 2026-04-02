<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'phone' => '+9725' . fake()->unique()->numerify('########'),
            'role' => fake()->randomElement(UserRole::cases()),
            'role_locked' => false,
            'preferred_language' => 'he',
            'is_active' => true,
            'session_timeout_minutes' => 60,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Admin,
            'role_locked' => true,
        ]);
    }

    public function coordinator(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Coordinator,
            'role_locked' => true,
        ]);
    }

    public function worker(): static
    {
        return $this->state(fn () => ['role' => UserRole::Worker]);
    }

    public function developer(): static
    {
        return $this->state(fn () => ['role' => UserRole::Developer]);
    }

    public function subcontractor(): static
    {
        return $this->state(fn () => ['role' => UserRole::Subcontractor]);
    }

    public function agency(): static
    {
        return $this->state(fn () => ['role' => UserRole::Agency]);
    }
}
