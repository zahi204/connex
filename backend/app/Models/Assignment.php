<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use App\Enums\EngagementType;
use Database\Factories\AssignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Assignment extends Model
{
    /** @use HasFactory<AssignmentFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'engagement_type' => EngagementType::class,
            'start_date' => 'date',
            'estimated_end_date' => 'date',
            'actual_end_date' => 'date',
            'price_per_day' => 'decimal:2',
            'contract_amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'status' => AssignmentStatus::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function resource(): MorphTo
    {
        return $this->morphTo();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ProjectNote::class);
    }
}
