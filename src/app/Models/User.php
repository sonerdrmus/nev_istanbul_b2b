<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'is_admin',
        'is_approved',
        'visible_currency_ids',
        'two_factor_secret',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_approved' => 'boolean',
            'visible_currency_ids' => 'array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return filled($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    public function plainTwoFactorSecret(): ?string
    {
        if (! filled($this->two_factor_secret)) {
            return null;
        }

        try {
            return decrypt($this->two_factor_secret);
        } catch (\Throwable) {
            return null;
        }
    }

    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->is_admin;
        }
        if ($panel->getId() === 'customer') {
            return false;
        }
        return false;
    }
}
