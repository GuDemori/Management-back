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
            'name'     => $dto->name,
            'email'    => $dto->email,
            'password' => $dto->password,
            'role'     => $dto->role,
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
}
