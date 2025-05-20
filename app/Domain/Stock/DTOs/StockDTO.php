<?php

namespace Domain\Stock\DTOs;

class StockDTO
{
    public function __construct(
        public ?int $id,
        public string $location,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            location: $data['location'],
        );
    }
}