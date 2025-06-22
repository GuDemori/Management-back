<?php

namespace App\Domain\Order\DTOs;

use App\Enums\OrderStatus;

class OrderResponseDTO
{
    public function __construct(
        public int $id,
        public int $clientId,
        public string $clientName,
        public string $addressStreet,
        public string $addressNumber,
        public string $addressDistrict,
        public string $addressCity,
        public string $addressState,
        public string $addressZipcode,
        /** @var OrderItemDTO[] */
        public array $items,
        public float $totalValue,
        public OrderStatus $status,
        /** @var OrderStatusHistoryDTO[] */
        public array $statusHistory,
        public string $createdAt,
        public string $updatedAt
    ) {}
}
