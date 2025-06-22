<?php

namespace App\Repositories;

use App\Domain\Order\Interfaces\OrderStatusHistoryRepositoryInterface;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Collection;

class OrderStatusHistoryRepository implements OrderStatusHistoryRepositoryInterface
{
    public function persist(OrderStatusHistory $history): void
    {
        $history->save();
    }

    public function findByOrderId(int $orderId): Collection
    {
        return OrderStatusHistory::where('order_id', $orderId)
            ->orderBy('changed_at')
            ->get();
    }
}