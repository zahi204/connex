<?php

namespace App\Models;

use App\Enums\BoqRequestStatus;
use App\Enums\BoqUrgency;
use App\Models\Traits\HasApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoqRequest extends Model
{
    use HasApproval;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'urgency' => BoqUrgency::class,
            'status' => BoqRequestStatus::class,
            'price' => 'decimal:2',
            'delivered_at' => 'datetime',
        ];
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_user_id');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(BoqLineItem::class);
    }
}
