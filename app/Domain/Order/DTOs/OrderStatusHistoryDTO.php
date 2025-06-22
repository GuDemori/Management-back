<?php

namespace App\Domain\Order\DTOs;

use App\Enums\OrderStatus;

class OrderStatusHistoryDTO
{
    public function __construct(
        public OrderStatus $oldStatus,
        public OrderStatus $newStatus,
        public string $changedAt,
        public int $changedByUserId
    ) {}
}