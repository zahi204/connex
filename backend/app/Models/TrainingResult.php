<?php

namespace App\Models;

use App\Enums\Suitability;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingResult extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'attendance_day1' => 'boolean',
            'attendance_day2' => 'boolean',
            'professional_score' => 'decimal:2',
            'suitability' => Suitability::class,
        ];
    }

    public function trainingCycle(): BelongsTo
    {
        return $this->belongsTo(TrainingCycle::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }
}
