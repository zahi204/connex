<?php

namespace Database\Factories;

use App\Enums\TrainingCycleStatus;
use App\Models\TrainingCycle;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingCycleFactory extends Factory
{
    protected $model = TrainingCycle::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-3 months', '+3 months');
        $endDate = (clone $startDate)->modify('+' . rand(7, 14) . ' days');

        return [
            'name' => 'Training Cycle ' . fake()->unique()->numerify('###'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => fake()->randomElement(TrainingCycleStatus::cases())->value,
        ];
    }
}
