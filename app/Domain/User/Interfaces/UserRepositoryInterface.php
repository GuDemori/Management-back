<?php

namespace Domain\User\Interfaces;

use Domain\User\DTOs\UserCreateDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(UserCreateDTO $dto): User;

    public function findByEmail(string $email): ?User;

    public function saveRefreshToken(int $userId, string $refreshToken, \DateTime $expiry): void;

    public function getUserByRefreshToken(string $refreshToken): ?User;
}
