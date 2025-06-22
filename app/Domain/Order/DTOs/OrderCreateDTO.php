<?php

namespace App\Domain\Order\DTOs;

class OrderCreateDTO
{
    public function __construct(
        public int $clientId,
        public string $clientName,
        public string $addressStreet,
        public string $addressNumber,
        public string $addressDistrict,
        public string $addressCity,
        public string $addressState,
        public string $addressZipcode,
        /** @var OrderItemDTO[] */
        public array $items
    ) {}
}
