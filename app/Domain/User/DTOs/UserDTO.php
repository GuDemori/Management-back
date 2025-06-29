<?php

namespace Domain\User\DTOs;

use App\Models\User;

class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $establishment_type_id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $role,
        public readonly string $document,
        public readonly ?string $cep,
        public readonly ?string $address,
        public readonly ?string $number,
        public readonly ?string $complement,
        public readonly ?string $district,
        public readonly ?string $city,
        public readonly ?string $state,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            establishment_type_id: $user->establishment_type_id,
            name: $user->name,
            email: $user->email,
            role: $user->role,
            document: $user->document,
            cep: $user->cep,
            address: $user->address,
            number: $user->number,
            complement: $user->complement,
            district: $user->district,
            city: $user->city,
            state: $user->state,
        );
    }
}
