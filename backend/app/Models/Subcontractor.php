<?php

namespace App\Models;

use App\Enums\SubcontractorStatus;
use App\Models\Traits\HasApproval;
use Database\Factories\SubcontractorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Subcontractor extends Model
{
    /** @use HasFactory<SubcontractorFactory> */
    use HasFactory, SoftDeletes, HasApproval;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'specializations' => 'array',
            'operating_areas' => 'array',
            'rating' => 'decimal:2',
            'status' => SubcontractorStatus::class,
            'availability_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments(): MorphMany
    {
        return $this->morphMany(Assignment::class, 'resource');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
