<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'establishment_type_id',
        'name',
        'email',
        'password',
        'role',
        'document',
        'cep',
        'address',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'refresh_token',
        'refresh_token_expiry',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN->value;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function establishmentType()
    {
        return $this->belongsTo(EstablishmentType::class);
    }

}
