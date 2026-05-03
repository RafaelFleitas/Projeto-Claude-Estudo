<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, \OwenIt\Auditing\Auditable;

    protected array $auditInclude = ['name', 'email', 'role'];

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function generatedReports(): HasMany
    {
        return $this->hasMany(GeneratedReport::class, 'generated_by');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'password'                => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'role'                    => UserRole::class,
        ];
    }
}
