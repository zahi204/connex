<?php

namespace App\Actions\Approval;

use App\Models\ApprovalQueue;
use App\Models\User;

class RequestChangesAction
{
    public function execute(ApprovalQueue $item, User $reviewer, string $notes): ApprovalQueue
    {
        $item->update([
            'status' => 'changes_required',
            'reviewed_by_user_id' => $reviewer->id,
            'reviewed_at' => now(),
            'change_notes' => $notes,
        ]);

        SendApprovalNotification::dispatch($item);

        return $item->fresh();
    }
}
