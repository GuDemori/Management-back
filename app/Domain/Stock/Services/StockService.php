<?php

namespace Domain\Stock\Services;

use Illuminate\Support\Collection;
use Domain\Stock\DTOs\StockDTO;
use Domain\Stock\Interfaces\StockServiceInterface;
use Domain\Stock\Interfaces\StockRepositoryInterface;

class StockService implements StockServiceInterface
{
    public function __construct(
        private StockRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): StockDTO
    {
        return $this->repository->find($id);
    }

    public function create(StockDTO $data): StockDTO
    {
        return $this->repository->create($data);
    }

    public function update(int $id, StockDTO $data): StockDTO
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}