<?php

namespace Domain\ProductStock\Services;

use Illuminate\Support\Collection;
use Domain\ProductStock\DTOs\ProductStockDTO;
use Domain\ProductStock\Interfaces\ProductStockServiceInterface;
use Domain\ProductStock\Interfaces\ProductStockRepositoryInterface;

class ProductStockService implements ProductStockServiceInterface
{
    public function __construct(
        private ProductStockRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $productId, int $stockId): ProductStockDTO
    {
        return $this->repository->find($productId, $stockId);
    }

    public function create(ProductStockDTO $dto): ProductStockDTO
    {
        return $this->repository->create($dto);
    }

    public function update(ProductStockDTO $dto): ProductStockDTO
    {
        return $this->repository->update($dto);
    }

    public function delete(int $productId, int $stockId): void
    {
        $this->repository->delete($productId, $stockId);
    }
}
