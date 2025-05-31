<?php

namespace Domain\User\DTOs;

class UserCreateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role,
        public readonly string $document,
        public readonly ?string $cep = null,
        public readonly ?string $address = null,
        public readonly ?string $number = null,
        public readonly ?string $complement = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
    ) {}
}