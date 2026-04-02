<?php

namespace App\Models;

use App\Enums\TrainingCycleStatus;
use Database\Factories\TrainingCycleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingCycle extends Model
{
    /** @use HasFactory<TrainingCycleFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => TrainingCycleStatus::class,
        ];
    }

    public function results(): HasMany
    {
        return $this->hasMany(TrainingResult::class);
    }
}
