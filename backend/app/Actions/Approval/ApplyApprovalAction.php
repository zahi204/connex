<?php

namespace App\Actions\Approval;

use App\Models\ApprovalQueue;

class ApplyApprovalAction
{
    public function execute(ApprovalQueue $item): void
    {
        $entity = $item->approvable;
        $data = $item->submitted_data;

        if (! $entity || ! $data) {
            return;
        }

        match ($item->action_type->value) {
            'profile_update' => $this->applyProfileUpdate($entity, $data),
            'project_submission' => $entity->update(['status' => 'under_review']),
            'document_upload' => $entity->update(['is_approved' => true]),
            'availability_change' => $entity->update(array_intersect_key($data, array_flip(['status', 'availability_date', 'available_daily', 'available_contract']))),
            'boq_request' => $entity->update(['status' => 'under_review']),
            default => null,
        };
    }

    private function applyProfileUpdate($entity, array $data): void
    {
        // Filter to only fillable attributes
        $fillable = collect($data)->except(['photo', 'documents', 'files'])->toArray();
        $entity->update($fillable);
    }
}
