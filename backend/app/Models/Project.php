<?php

namespace App\Models;

use App\Enums\ProjectSource;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Traits\HasApproval;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, SoftDeletes, HasApproval;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'project_type' => ProjectType::class,
            'estimated_start_date' => 'date',
            'estimated_completion' => 'date',
            'status' => ProjectStatus::class,
            'source' => ProjectSource::class,
            'required_trades' => 'array',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ProjectNote::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'current_project_id');
    }
}
