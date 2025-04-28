<?php

namespace App\Domain\Product\DTOs;

class ProductDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $stock_id,
        public int $supplier_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            price: (float) $data['price'],
            stock_id: (int) $data['stock_id'],
            supplier_id: (int) $data['supplier_id'],
        );
    }
}