<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'role_locked' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'hashed',
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() && $this->is_active;
    }

    public function getFilamentName(): string
    {
        $name = $this->getAttribute('name');
        if (is_string($name) && $name !== '') {
            return $name;
        }

        if (is_string($this->email) && $this->email !== '') {
            return $this->email;
        }

        if (is_string($this->phone) && $this->phone !== '') {
            return $this->phone;
        }

        return 'User #'.$this->getKey();
    }
}
