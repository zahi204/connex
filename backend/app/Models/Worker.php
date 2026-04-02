<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\Skill;
use App\Enums\WorkArea;
use App\Enums\WorkerStatus;
use App\Models\Traits\HasApproval;
use Database\Factories\WorkerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    /** @use HasFactory<WorkerFactory> */
    use HasFactory, SoftDeletes, HasApproval;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'date_of_arrival' => 'date',
            'available_daily' => 'boolean',
            'available_contract' => 'boolean',
            'training_score' => 'decimal:2',
            'professional_rating' => 'decimal:2',
            'reliability_rating' => 'decimal:2',
            'suitable_for_leader' => 'boolean',
            'status' => WorkerStatus::class,
            'payment_status' => PaymentStatus::class,
            'last_payment_date' => 'date',
            'eligible_for_assignment' => 'boolean',
            'blocked' => 'boolean',
            'last_training_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staffingAgency(): BelongsTo
    {
        return $this->belongsTo(StaffingAgency::class);
    }

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function trainingResults(): HasMany
    {
        return $this->hasMany(TrainingResult::class);
    }

    public function assignments(): MorphMany
    {
        return $this->morphMany(Assignment::class, 'resource');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    // Scopes

    public function scopeAvailable($query)
    {
        return $query->where('status', WorkerStatus::Available);
    }

    public function scopeBySkill($query, Skill $skill)
    {
        return $query->where(function ($q) use ($skill) {
            $q->where('primary_skill', $skill)
              ->orWhere('secondary_skill', $skill);
        });
    }

    public function scopeByRegion($query, WorkArea $area)
    {
        return $query->where('preferred_work_area', $area);
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', PaymentStatus::Overdue);
    }
}
