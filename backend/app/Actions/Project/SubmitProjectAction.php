<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\User;

class SubmitProjectAction
{
    public function execute(User $user, array $data): Project
    {
        $project = Project::create([
            'created_by_user_id' => $user->id,
            'developer_id' => $user->profileable_id,
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'region' => $data['region'] ?? null,
            'project_type' => $data['project_type'],
            'estimated_start_date' => $data['estimated_start_date'] ?? null,
            'estimated_completion' => $data['estimated_completion'] ?? null,
            'required_trades' => $data['required_trades'] ?? [],
            'notes' => $data['notes'] ?? null,
            'status' => 'new',
            'source' => 'manual',
        ]);

        // If submitted by a developer user, create approval entry
        if ($user->role?->value === 'developer') {
            $project->submitForApproval('project_submission', $data, $user);
        }

        return $project;
    }
}
