<?php

namespace App\Actions\Approval;

use App\Models\ApprovalQueue;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SubmitForApprovalAction
{
    public function execute(Model $entity, string $actionType, array $data, User $user): ApprovalQueue
    {
        return $entity->submitForApproval($actionType, $data, $user);
    }
}
