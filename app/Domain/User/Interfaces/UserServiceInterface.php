<?php

namespace Domain\User\Interfaces;

use Domain\User\DTOs\UserCreateDTO;
use Domain\User\DTOs\UserLoginDTO;
use Domain\User\DTOs\UserAuthResponseDTO;

interface UserServiceInterface
{
    public function register(UserCreateDTO $dto): void;

    public function login(UserLoginDTO $dto): UserAuthResponseDTO;

    public function refreshToken(string $refreshToken): UserAuthResponseDTO;
}
