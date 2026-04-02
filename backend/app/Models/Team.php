<?php

namespace App\Models;

use App\Enums\TeamStatus;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => TeamStatus::class,
            'availability_date' => 'date',
            'average_rating' => 'decimal:2',
        ];
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'team_leader_id');
    }

    public function currentProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'current_project_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function assignments(): MorphMany
    {
        return $this->morphMany(Assignment::class, 'resource');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->whereIn('status', [TeamStatus::Available, TeamStatus::Assigned, TeamStatus::AvailableSoon]);
    }

    // Accessors

    public function recalculateAverageRating(): void
    {
        $avg = $this->members()
            ->join('workers', 'team_members.worker_id', '=', 'workers.id')
            ->whereNull('team_members.left_at')
            ->avg('workers.professional_rating');

        $this->update(['average_rating' => $avg ?? 0]);
    }
}
