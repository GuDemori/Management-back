<?php

namespace App\Domain\Order\Interfaces;

use App\Domain\Order\DTOs\OrderCreateDTO;
use App\Domain\Order\DTOs\OrderUpdateDTO;
use App\Domain\Order\DTOs\OrderResponseDTO;
use Illuminate\Support\Collection;

interface OrderServiceInterface
{
    public function create(OrderCreateDTO $dto): OrderResponseDTO;

    public function update(OrderUpdateDTO $dto): OrderResponseDTO;

    public function findById(int $id): OrderResponseDTO;

    public function list(array $filters = []): Collection;
}