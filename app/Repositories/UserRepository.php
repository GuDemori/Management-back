<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Domain\User\DTOs\UserCreateDTO;
use Domain\User\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(UserCreateDTO $dto): User
    {
        return User::create([
            'establishment_type_id' => $dto->establishment_type_id,
            'name'                  => $dto->name,
            'email'                 => $dto->email,
            'password'              => $dto->password,
            'role'                  => $dto->role,
            'document'              => $dto->document,
            'cep'                   => $dto->cep,
            'address'               => $dto->address,
            'number'                => $dto->number,
            'complement'            => $dto->complement,
            'district'              => $dto->district,
            'city'                  => $dto->city,
            'state'                 => $dto->state,
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function saveRefreshToken(int $userId, string $refreshToken, \DateTime $expiry): void
    {
        User::where('id', $userId)->update([
            'refresh_token'        => $refreshToken,
            'refresh_token_expiry' => Carbon::instance($expiry),
        ]);
    }

    public function getUserByRefreshToken(string $refreshToken): ?User
    {
        return User::where('refresh_token', $refreshToken)->first();
    }

    public function revokeRefreshToken(int $userId): void
    {
        User::where('id', $userId)->update([
            'refresh_token' => null,
            'refresh_token_expiry' => null,
        ]);
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }
}
