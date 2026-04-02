<?php

namespace Database\Factories;

use App\Enums\AssignmentStatus;
use App\Enums\EngagementType;
use App\Models\Assignment;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');

        return [
            'project_id' => Project::factory(),
            'resource_type' => Worker::class,
            'resource_id' => Worker::factory(),
            'engagement_type' => fake()->randomElement(EngagementType::cases())->value,
            'start_date' => $startDate,
            'estimated_end_date' => fake()->dateTimeBetween($startDate, '+6 months'),
            'work_description' => fake()->sentence(),
            'price_per_day' => fake()->randomFloat(2, 200, 800),
            'status' => fake()->randomElement(AssignmentStatus::cases())->value,
        ];
    }
}
