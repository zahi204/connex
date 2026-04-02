<?php

namespace App\Models;

use App\Enums\UnitOfMeasure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoqLineItem extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'unit_of_measure' => UnitOfMeasure::class,
            'quantity' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function boqRequest(): BelongsTo
    {
        return $this->belongsTo(BoqRequest::class);
    }
}
