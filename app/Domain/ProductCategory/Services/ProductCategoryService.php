<?php

namespace App\Domain\ProductCategory\Services;

use App\Domain\ProductCategory\DTOs\ProductCategoryDTO;
use App\Domain\ProductCategory\Interfaces\ProductCategoryServiceInterface;
use App\Domain\ProductCategory\Interfaces\ProductCategoryRepositoryInterface;

class ProductCategoryService implements ProductCategoryServiceInterface
{
    private ProductCategoryRepositoryInterface $repository;

    public function __construct(ProductCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function findById(int $id): ?ProductCategoryDTO
    {
        return $this->repository->findById($id);
    }

    public function findByCode(string $code): ?ProductCategoryDTO
    {
        return $this->repository->findByCode($code);
    }

    public function create(ProductCategoryDTO $dto): ProductCategoryDTO
    {
        if ($this->repository->findByCode($dto->code)) {
            throw new \InvalidArgumentException('Product category code already exists.');
        }

        return $this->repository->create($dto);
    }

    public function update(ProductCategoryDTO $dto): bool
    {
        $existing = $this->repository->findById($dto->id);
        if (! $existing) {
            throw new \RuntimeException('Product category not found.');
        }

        if ($dto->code !== $existing->code && $this->repository->findByCode($dto->code)) {
            throw new \InvalidArgumentException('Product category code already in use.');
        }

        return $this->repository->update($dto);
    }

    public function delete(int $id): bool
    {
        $existing = $this->repository->findById($id);
        if (! $existing) {
            throw new \RuntimeException('Product category not found.');
        }

        return $this->repository->delete($id);
    }
}