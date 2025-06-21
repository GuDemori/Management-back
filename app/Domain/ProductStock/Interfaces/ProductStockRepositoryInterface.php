<?php

namespace Domain\ProductStock\Interfaces;

use Domain\ProductStock\DTOs\ProductStockDTO;
use Illuminate\Support\Collection;

interface ProductStockRepositoryInterface
{
    public function all(): Collection;
    public function find(int $productId, int $stockId): ProductStockDTO;
    public function create(ProductStockDTO $dto): ProductStockDTO;
    public function update(ProductStockDTO $dto): ProductStockDTO;
    public function delete(int $productId, int $stockId): void;
}
