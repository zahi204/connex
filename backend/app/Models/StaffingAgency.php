<?php

namespace App\Models;

use App\Models\Traits\HasApproval;
use Database\Factories\StaffingAgencyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class StaffingAgency extends Model
{
    /** @use HasFactory<StaffingAgencyFactory> */
    use HasFactory, SoftDeletes, HasApproval;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'worker_types' => 'array',
            'countries_of_origin' => 'array',
            'average_quality' => 'decimal:2',
            'payments_made' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class, 'staffing_agency_id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    // Computed attributes

    public function getTrainingBillingAttribute(): float
    {
        return ($this->workers_trained ?? 0) * 3000;
    }

    public function getAverageWorkerRatingAttribute(): ?float
    {
        return $this->workers()->avg('professional_rating');
    }
}
