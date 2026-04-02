<?php

namespace App\Actions\Approval;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BulkApproveAction
{
    public function __construct(
        private ApproveItemAction $approveAction,
    ) {}

    public function execute(Collection $items, User $reviewer): void
    {
        DB::transaction(function () use ($items, $reviewer) {
            foreach ($items as $item) {
                if ($item->status->value === 'pending') {
                    $this->approveAction->execute($item, $reviewer);
                }
            }
        });
    }
}
