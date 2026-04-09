<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $guarded = ['id'];

    protected $hidden = [
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'role_locked' => 'boolean',
            'is_active' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'session_timeout_minutes' => 'integer',
        ];
    }

    public function profile(): MorphTo
    {
        return $this->morphTo('profileable');
    }

    /**
     * True when the user chose a role but hasn't completed the wizard yet.
     * role_locked is set to true only after wizard completion.
     */
    public function needsOnboarding(): bool
    {
        if ($this->role === null || $this->role_locked) {
            return false;
        }

        return in_array($this->role, [
            UserRole::Worker,
            UserRole::Developer,
            UserRole::Subcontractor,
            UserRole::Agency,
        ]);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::Admin, UserRole::Coordinator]);
    }
}
