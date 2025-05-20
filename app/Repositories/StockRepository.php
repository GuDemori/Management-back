<?php

namespace App\Repositories;

use App\Models\Stock;
use Illuminate\Support\Collection;
use Domain\Stock\DTOs\StockDTO;
use Domain\Stock\Interfaces\StockRepositoryInterface;

class StockRepository implements StockRepositoryInterface
{
    public function all(): Collection
    {
        return Stock::all()->map(fn($stock) => new StockDTO($stock->id, $stock->location));
    }

    public function find(int $id): StockDTO
    {
        $stock = Stock::findOrFail($id);
        return new StockDTO($stock->id, $stock->location);
    }

    public function create(StockDTO $data): StockDTO
    {
        $stock = Stock::create(['location' => $data->location]);
        return new StockDTO($stock->id, $stock->location);
    }

    public function update(int $id, StockDTO $data): StockDTO
    {
        $stock = Stock::findOrFail($id);
        $stock->update(['location' => $data->location]);
        return new StockDTO($stock->id, $stock->location);
    }

    public function delete(int $id): void
    {
        Stock::destroy($id);
    }
}