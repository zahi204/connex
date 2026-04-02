<?php

namespace App\Actions\Approval;

use App\Models\ApprovalQueue;
use App\Models\User;

class RejectItemAction
{
    public function execute(ApprovalQueue $item, User $reviewer, string $reason): ApprovalQueue
    {
        $item->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        SendApprovalNotification::dispatch($item);

        return $item->fresh();
    }
}
