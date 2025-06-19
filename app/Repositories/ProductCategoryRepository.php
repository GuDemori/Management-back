<?php

namespace App\Repositories;

use App\Domain\ProductCategory\DTOs\ProductCategoryDTO;
use App\Domain\ProductCategory\Interfaces\ProductCategoryRepositoryInterface;
use App\Models\ProductCategory;

class ProductCategoryRepository implements ProductCategoryRepositoryInterface
{
    public function all(): array
    {
        return ProductCategory::all()
            ->map(fn(ProductCategory $m) => ProductCategoryDTO::fromModel($m))
            ->toArray();
    }

    public function findById(int $id): ?ProductCategoryDTO
    {
        $m = ProductCategory::find($id);
        return $m ? ProductCategoryDTO::fromModel($m) : null;
    }

    public function findByCode(string $code): ?ProductCategoryDTO
    {
        $m = ProductCategory::where('code', $code)->first();
        return $m ? ProductCategoryDTO::fromModel($m) : null;
    }

    public function create(ProductCategoryDTO $dto): ProductCategoryDTO
    {
        $m = ProductCategory::create([
            'name' => $dto->name,
            'code' => $dto->code,
        ]);
        return ProductCategoryDTO::fromModel($m);
    }

    public function update(ProductCategoryDTO $dto): bool
    {
        $m = ProductCategory::find($dto->id);
        if (! $m) {
            return false;
        }
        return $m->update([
            'name' => $dto->name,
            'code' => $dto->code,
        ]);
    }

    public function delete(int $id): bool
    {
        $m = ProductCategory::find($id);
        if (! $m) {
            return false;
        }
        return (bool) $m->delete();
    }
}
