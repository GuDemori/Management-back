<?php

namespace App\Domain\Order\DTOs;

class OrderItemDTO
{
    public function __construct(
        public int $productId,
        public string $productName,
        public int $quantity,
        public float $priceUnit,
        public float $subtotal
    ) {}
}