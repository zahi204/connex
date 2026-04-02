<?php

namespace App\Models;

use App\Enums\ApprovalActionType;
use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApprovalQueue extends Model
{
    protected $table = 'approval_queue';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'action_type' => ApprovalActionType::class,
            'status' => ApprovalStatus::class,
            'submitted_data' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', ApprovalStatus::Pending);
    }

    public function scopeByType($query, ApprovalActionType $type)
    {
        return $query->where('action_type', $type);
    }

    public function scopeStale($query)
    {
        return $query->where('status', ApprovalStatus::Pending)
            ->where('created_at', '<', now()->subHours(48));
    }
}
