<?php

namespace Domain\User\DTOs;

class UserLoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {}
}
