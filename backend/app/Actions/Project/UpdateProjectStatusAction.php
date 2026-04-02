<?php

namespace App\Actions\Project;

use App\Enums\ProjectStatus;
use App\Models\Project;

class UpdateProjectStatusAction
{
    private const ALLOWED_TRANSITIONS = [
        'new' => ['under_review', 'cancelled'],
        'under_review' => ['resources_assigned', 'cancelled'],
        'resources_assigned' => ['in_progress', 'cancelled'],
        'in_progress' => ['completed', 'cancelled'],
    ];

    public function execute(Project $project, string $newStatus): Project
    {
        $current = $project->status->value;
        $allowed = self::ALLOWED_TRANSITIONS[$current] ?? [];

        if (! in_array($newStatus, $allowed)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$current}' to '{$newStatus}'."
            );
        }

        $project->update(['status' => ProjectStatus::from($newStatus)]);

        return $project->fresh();
    }
}
