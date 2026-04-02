<?php

namespace Database\Factories;

use App\Models\Developer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeveloperFactory extends Factory
{
    protected $model = Developer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->developer(),
            'company_name' => fake()->company() . ' Development',
            'registration_number' => fake()->numerify('51#######'),
            'phone' => '+9725' . fake()->numerify('########'),
            'email' => fake()->companyEmail(),
            'company_description' => fake()->paragraph(),
            'areas_of_operation' => fake()->randomElements(['north', 'center', 'south'], rand(1, 3)),
            'company_size' => fake()->randomElement(['small', 'medium', 'large']),
            'specializations' => fake()->randomElements(['residential', 'commercial', 'infrastructure', 'transportation', 'public'], rand(1, 3)),
            'contact_person_name' => fake()->name(),
            'contact_person_role' => fake()->randomElement(['CEO', 'VP Operations', 'Project Manager']),
            'contact_person_phone' => '+9725' . fake()->numerify('########'),
            'contact_person_email' => fake()->email(),
        ];
    }
}
