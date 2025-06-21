<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Domain\ProductStock\DTOs\ProductStockDTO;
use Domain\ProductStock\Interfaces\ProductStockRepositoryInterface;

class ProductStockRepository implements ProductStockRepositoryInterface
{
    protected string $table = 'product_stock';

    public function all(): Collection
    {
        return DB::table($this->table)
            ->get()
            ->map(fn($row) => $this->mapToDTO((array) $row));
    }

    public function find(int $productId, int $stockId): ProductStockDTO
    {
        $data = DB::table($this->table)
            ->where('product_id', $productId)
            ->where('stock_id', $stockId)
            ->first();

        if (! $data) {
            abort(404, 'Relação produto/estoque não encontrada.');
        }

        return $this->mapToDTO((array) $data);
    }

    public function create(ProductStockDTO $dto): ProductStockDTO
    {
        DB::table($this->table)->insert([
            'product_id' => $dto->product_id,
            'stock_id'   => $dto->stock_id,
            'quantity'   => $dto->quantity,
            'min_stock'  => $dto->min_stock,
            'isActive'   => $dto->isActive,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $dto;
    }

    public function update(ProductStockDTO $dto): ProductStockDTO
    {
        DB::table($this->table)
            ->where('product_id', $dto->product_id)
            ->where('stock_id', $dto->stock_id)
            ->update([
                'quantity'   => $dto->quantity,
                'min_stock'  => $dto->min_stock,
                'isActive'   => $dto->isActive,
                'updated_at' => now(),
            ]);

        return $dto;
    }

    public function delete(int $productId, int $stockId): void
    {
        DB::table($this->table)
            ->where('product_id', $productId)
            ->where('stock_id', $stockId)
            ->delete();
    }

    private function mapToDTO(array $data): ProductStockDTO
    {
        return new ProductStockDTO(
            product_id: $data['product_id'],
            stock_id: $data['stock_id'],
            quantity: $data['quantity'],
            min_stock: $data['min_stock'],
            isActive: $data['isActive'],
        );
    }
}