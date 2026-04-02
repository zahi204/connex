<?php

namespace Database\Factories;

use App\Enums\Skill;
use App\Enums\WorkArea;
use App\Enums\WorkerStatus;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkerFactory extends Factory
{
    protected $model = Worker::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->worker(),
            'full_name' => fake()->name(),
            'id_number' => fake()->numerify('#########'),
            'phone' => '+9725' . fake()->numerify('########'),
            'country_of_origin' => fake()->randomElement(['Philippines', 'China', 'Moldova', 'Ukraine', 'Thailand', 'India']),
            'languages' => fake()->randomElements(['Hebrew', 'English', 'Arabic', 'Russian', 'Chinese', 'Thai'], rand(1, 3)),
            'date_of_arrival' => fake()->dateTimeBetween('-5 years', '-1 month'),
            'primary_skill' => fake()->randomElement(Skill::cases())->value,
            'secondary_skill' => fake()->optional(0.5)->randomElement(array_map(fn ($s) => $s->value, Skill::cases())),
            'preferred_work_area' => fake()->randomElement(WorkArea::cases())->value,
            'available_daily' => true,
            'available_contract' => fake()->boolean(),
            'training_score' => fake()->optional(0.7)->randomFloat(2, 50, 100),
            'professional_rating' => fake()->optional(0.6)->randomFloat(2, 1, 5),
            'reliability_rating' => fake()->optional(0.6)->randomFloat(2, 1, 5),
            'suitable_for_leader' => fake()->boolean(20),
            'status' => fake()->randomElement(WorkerStatus::cases())->value,
            'payment_status' => 'paid',
            'eligible_for_assignment' => true,
            'blocked' => false,
        ];
    }
}
