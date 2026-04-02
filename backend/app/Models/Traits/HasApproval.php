<?php

namespace App\Models\Traits;

use App\Models\ApprovalQueue;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasApproval
{
    public function approvals(): MorphMany
    {
        return $this->morphMany(ApprovalQueue::class, 'approvable');
    }

    public function pendingApprovals(): MorphMany
    {
        return $this->approvals()->where('status', 'pending');
    }

    public function submitForApproval(string $actionType, array $data, User $user): ApprovalQueue
    {
        return $this->approvals()->create([
            'submitted_by_user_id' => $user->id,
            'action_type' => $actionType,
            'submitted_data' => $data,
            'status' => 'pending',
        ]);
    }
}
