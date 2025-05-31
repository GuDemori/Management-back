<?php

namespace Domain\User\DTOs;

class UserAuthResponseDTO
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $refreshToken,
        public readonly int $expiresIn,
    ) {}
}