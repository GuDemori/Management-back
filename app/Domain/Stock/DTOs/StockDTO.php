<?php

namespace Domain\Stock\DTOs;

class StockDTO
{
    public function __construct(
        public ?int $id,
        public string $cep,
        public ?string $address,
        public ?string $number,
        public ?string $city,
        public ?string $state,
        public bool $isActive = true
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            cep: $data['cep'],
            address: $data['address'] ?? null,
            number: $data['number'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            isActive: $data['isActive'] ?? true,
        );
    }
}