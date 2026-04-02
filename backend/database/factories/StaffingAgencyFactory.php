<?php

namespace Database\Factories;

use App\Models\StaffingAgency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffingAgencyFactory extends Factory
{
    protected $model = StaffingAgency::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->agency(),
            'agency_name' => fake()->company() . ' Staffing',
            'registration_number' => fake()->numerify('51#######'),
            'phone' => '+9725' . fake()->numerify('########'),
            'email' => fake()->companyEmail(),
            'contact_person_name' => fake()->name(),
            'contact_person_role' => fake()->randomElement(['CEO', 'Operations Manager', 'HR Director']),
            'contact_person_phone' => '+9725' . fake()->numerify('########'),
            'contact_person_email' => fake()->email(),
            'worker_types' => fake()->randomElements(['formwork', 'rebar', 'general', 'finishing'], rand(1, 3)),
            'countries_of_origin' => fake()->randomElements(['Philippines', 'China', 'Moldova', 'Ukraine', 'Thailand', 'India'], rand(1, 4)),
            'average_capacity' => fake()->numberBetween(20, 200),
            'monthly_throughput' => fake()->numberBetween(5, 50),
            'workers_transferred' => fake()->numberBetween(0, 500),
            'workers_trained' => fake()->numberBetween(0, 300),
            'average_quality' => fake()->optional(0.5)->randomFloat(2, 2, 5),
            'payments_made' => fake()->randomFloat(2, 10000, 500000),
            'outstanding_balance' => fake()->randomFloat(2, 0, 50000),
        ];
    }
}
