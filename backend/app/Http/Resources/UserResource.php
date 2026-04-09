<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'role' => $this->role?->value,
            'role_locked' => $this->role_locked,
            'needs_onboarding' => $this->needsOnboarding(),
            'preferred_language' => $this->preferred_language,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->toISOString(),
            'profile' => $this->whenLoaded('profile'),
        ];
    }
}
