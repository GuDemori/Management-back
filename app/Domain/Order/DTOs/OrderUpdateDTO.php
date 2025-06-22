<?php

namespace App\Domain\Order\DTOs;

use App\Enums\OrderStatus;

class OrderUpdateDTO
{
    public function __construct(
        public int $orderId,
        /** @var OrderItemDTO[]|null */
        public ?array $items = null,
        public ?OrderStatus $newStatus = null,
        public ?int $changedByUserId = null
    ) {}
}
