<?php

namespace App\Actions\Approval;

use App\Models\ApprovalQueue;
use App\Models\User;

class ApproveItemAction
{
    public function __construct(
        private ApplyApprovalAction $applyAction,
    ) {}

    public function execute(ApprovalQueue $item, User $reviewer): ApprovalQueue
    {
        $item->update([
            'status' => 'approved',
            'reviewed_by_user_id' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        $this->applyAction->execute($item);

        SendApprovalNotification::dispatch($item);

        return $item->fresh();
    }
}
