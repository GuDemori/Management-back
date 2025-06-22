<?php

namespace App\Domain\Order\Interfaces;

use App\Models\OrderItemHistory;
use Illuminate\Support\Collection;

interface OrderItemHistoryRepositoryInterface
{
    public function persist(OrderItemHistory $history): void;

    public function findByOrderItemId(int $itemId): Collection;
}