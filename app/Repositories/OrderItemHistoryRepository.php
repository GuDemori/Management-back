<?php

namespace App\Repositories;

use App\Domain\Order\Interfaces\OrderItemHistoryRepositoryInterface;
use App\Models\OrderItemHistory;
use Illuminate\Support\Collection;

class OrderItemHistoryRepository implements OrderItemHistoryRepositoryInterface
{
    public function persist(OrderItemHistory $history): void
    {
        $history->save();
    }

    public function findByOrderItemId(int $itemId): Collection
    {
        return OrderItemHistory::where('order_item_id', $itemId)
            ->orderBy('changed_at')
            ->get();
    }
}