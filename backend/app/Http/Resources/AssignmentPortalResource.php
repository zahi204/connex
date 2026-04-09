<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Whitelisted assignment fields safe for worker portal display.
 * Financial terms (price_per_day, contract_amount, commission_rate) are excluded per Spec 11.3.
 */
class AssignmentPortalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status?->value ?? $this->status,
            'engagement_type' => $this->engagement_type?->value ?? $this->engagement_type,
            'start_date' => $this->start_date?->toDateString(),
            'estimated_end_date' => $this->estimated_end_date?->toDateString(),
            'actual_end_date' => $this->actual_end_date?->toDateString(),
            'work_description' => $this->work_description,
            'notes' => $this->notes,
            'project' => $this->whenLoaded('project', fn () => [
                'id' => $this->project->id,
                'name' => $this->project->name,
                'description' => $this->project->description ?? null,
                'status' => $this->project->status?->value ?? $this->project->status,
            ]),
        ];
    }
}
