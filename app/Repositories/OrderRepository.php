<?php

namespace App\Repositories;

use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function persist(Order $order): void
    {
        $order->save();
    }

    public function update(Order $order): void
    {
        $order->save();
    }

    public function find(int $id): ?Order
    {
        return Order::with(['items', 'statusHistories'])->find($id);
    }

    public function findAll(array $filters = []): Collection
    {
        $query = Order::query();

        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with('items')->get();
    }
}