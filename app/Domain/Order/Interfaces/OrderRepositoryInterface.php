<?php

namespace App\Domain\Order\Interfaces;

use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function persist(Order $order): void;

    public function update(Order $order): void;

    public function find(int $id): ?Order;

    public function findAll(array $filters = []): Collection;
}