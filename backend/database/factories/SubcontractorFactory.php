<?php

namespace Database\Factories;

use App\Enums\SubcontractorStatus;
use App\Models\Subcontractor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubcontractorFactory extends Factory
{
    protected $model = Subcontractor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->subcontractor(),
            'name' => fake()->company(),
            'registration_number' => fake()->numerify('51#######'),
            'phone' => '+9725' . fake()->numerify('########'),
            'email' => fake()->companyEmail(),
            'specializations' => fake()->randomElements(['formwork', 'rebar', 'general', 'finishing', 'skeleton', 'electrical', 'plumbing'], rand(1, 3)),
            'operating_areas' => fake()->randomElements(['north', 'center', 'south'], rand(1, 3)),
            'project_size' => fake()->randomElement(['small', 'medium', 'large']),
            'number_of_workers' => fake()->numberBetween(5, 100),
            'years_of_experience' => fake()->numberBetween(1, 30),
            'rating' => fake()->optional(0.6)->randomFloat(2, 2, 5),
            'status' => fake()->randomElement(SubcontractorStatus::cases())->value,
        ];
    }
}
