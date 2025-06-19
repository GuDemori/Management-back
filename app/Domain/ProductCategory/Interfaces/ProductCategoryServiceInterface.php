<?php

namespace App\Domain\ProductCategory\Interfaces;

use App\Domain\ProductCategory\DTOs\ProductCategoryDTO;

interface ProductCategoryServiceInterface
{
    public function all(): array;

    public function findById(int $id): ?ProductCategoryDTO;

    public function findByCode(string $code): ?ProductCategoryDTO;

    public function create(ProductCategoryDTO $dto): ProductCategoryDTO;

    public function update(ProductCategoryDTO $dto): bool;

    public function delete(int $id): bool;
}
