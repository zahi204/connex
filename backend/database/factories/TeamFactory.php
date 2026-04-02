<?php

namespace Database\Factories;

use App\Enums\TeamStatus;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => 'Team-' . fake()->unique()->numerify('###'),
            'team_type' => fake()->randomElement(['skeleton', 'formwork', 'rebar', 'general', 'project_specific']),
            'primary_field' => fake()->randomElement(['formwork', 'rebar', 'general', 'finishing', 'skeleton']),
            'operating_area' => fake()->randomElement(['north', 'center', 'south', 'flexible']),
            'status' => fake()->randomElement(TeamStatus::cases())->value,
            'work_types' => fake()->randomElement(['daily', 'contract', 'both']),
            'average_rating' => fake()->optional(0.5)->randomFloat(2, 2, 5),
        ];
    }
}
