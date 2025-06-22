<?php

namespace Domain\ProductStock\DTOs;

class ProductStockDTO
{
    public function __construct(
        public int $product_id,
        public int $stock_id,
        public int $quantity,
        public int $min_stock = 0,
        public bool $isActive = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: $data['product_id'],
            stock_id: $data['stock_id'],
            quantity: $data['quantity'],
            min_stock: $data['min_stock'] ?? 0,
            isActive: $data['isActive'] ?? true,
        );
    }
}
