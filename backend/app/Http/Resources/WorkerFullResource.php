<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerFullResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'id_number' => $this->id_number,
            'phone' => $this->user?->phone,
            'primary_skill' => $this->primary_skill?->value,
            'secondary_skill' => $this->secondary_skill?->value,
            'preferred_work_area' => $this->preferred_work_area?->value,
            'languages' => $this->languages,
            'country_of_origin' => $this->country_of_origin,
            'date_of_arrival' => $this->date_of_arrival?->toDateString(),
            'available_daily' => $this->available_daily,
            'available_contract' => $this->available_contract,
            'previous_experience' => $this->previous_experience,
            'training_score' => $this->training_score,
            'professional_rating' => $this->professional_rating,
            'reliability_rating' => $this->reliability_rating,
            'suitable_for_leader' => $this->suitable_for_leader,
            'status' => $this->status?->value,
            'payment_status' => $this->payment_status?->value,
            'last_payment_date' => $this->last_payment_date?->toDateString(),
            'eligible_for_assignment' => $this->eligible_for_assignment,
            'blocked' => $this->blocked,
            'last_training_date' => $this->last_training_date?->toDateString(),
            'general_notes' => $this->general_notes,
            'staffing_agency_id' => $this->staffing_agency_id,
            'photo_url' => $this->photo ?: null,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
