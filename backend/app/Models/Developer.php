<?php

namespace App\Models;

use App\Models\Traits\HasApproval;
use Database\Factories\DeveloperFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Developer extends Model implements HasMedia
{
    /** @use HasFactory<DeveloperFactory> */
    use HasApproval, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'areas_of_operation' => 'array',
            'specializations' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }
}
