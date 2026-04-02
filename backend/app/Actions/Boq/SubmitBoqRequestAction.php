<?php

namespace App\Actions\Boq;

use App\Models\BoqRequest;
use App\Models\User;

class SubmitBoqRequestAction
{
    public function execute(User $user, array $data): BoqRequest
    {
        $request = BoqRequest::create([
            'requested_by_user_id' => $user->id,
            'project_name' => $data['project_name'] ?? null,
            'city' => $data['city'] ?? null,
            'region' => $data['region'] ?? null,
            'project_type' => $data['project_type'] ?? null,
            'scope_description' => $data['scope_description'] ?? null,
            'urgency' => $data['urgency'] ?? 'standard',
            'status' => 'new',
        ]);

        $request->submitForApproval('boq_request', $data, $user);

        return $request;
    }
}
