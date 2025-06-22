<?php

namespace App\Domain\Order\Interfaces;

use App\Models\OrderStatusHistory;
use Illuminate\Support\Collection;

interface OrderStatusHistoryRepositoryInterface
{
    public function persist(OrderStatusHistory $history): void;

    public function findByOrderId(int $orderId): Collection;
}