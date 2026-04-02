<?php

namespace Database\Factories;

use App\Enums\ProjectSource;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Developer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');

        return [
            'developer_id' => Developer::factory(),
            'created_by_user_id' => User::factory()->admin(),
            'name' => fake()->randomElement(['Tower', 'Park', 'Mall', 'Bridge', 'Residence']) . ' ' . fake()->city(),
            'address' => fake()->streetAddress(),
            'city' => fake()->randomElement(['Tel Aviv', 'Jerusalem', 'Haifa', 'Beer Sheva', 'Netanya', 'Ashdod']),
            'region' => fake()->randomElement(['north', 'center', 'south']),
            'project_type' => fake()->randomElement(ProjectType::cases())->value,
            'estimated_start_date' => $startDate,
            'estimated_completion' => fake()->dateTimeBetween($startDate, '+2 years'),
            'status' => fake()->randomElement(ProjectStatus::cases())->value,
            'source' => ProjectSource::Manual->value,
            'required_trades' => fake()->randomElements(['formwork', 'rebar', 'general', 'finishing', 'skeleton'], rand(1, 4)),
        ];
    }
}
