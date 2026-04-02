<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'primary_skill' => $this->primary_skill?->value,
            'secondary_skill' => $this->secondary_skill?->value,
            'preferred_work_area' => $this->preferred_work_area?->value,
            'languages' => $this->languages,
            'country_of_origin' => $this->country_of_origin,
            'available_daily' => $this->available_daily,
            'available_contract' => $this->available_contract,
            'training_score' => $this->training_score,
            'professional_rating' => $this->professional_rating,
            'reliability_rating' => $this->reliability_rating,
            'suitable_for_leader' => $this->suitable_for_leader,
            'status' => $this->status?->value,
            'eligible_for_assignment' => $this->eligible_for_assignment,
            'last_training_date' => $this->last_training_date?->toDateString(),
            'photo_url' => $this->getFirstMediaUrl('photo'),
        ];
    }
}
