<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\Interfaces\ProductNicknameServiceInterface;
use App\Domain\Product\Interfaces\ProductNicknameRepositoryInterface;
use App\Domain\Product\DTOs\ProductNicknameDTO;
use App\Models\ProductNickname;
use Illuminate\Database\Eloquent\Collection;

class ProductNicknameService implements ProductNicknameServiceInterface
{
    public function __construct(
        protected ProductNicknameRepositoryInterface $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    public function getByProduct(int $productId): Collection
    {
        return $this->repository->getByProduct($productId);
    }

    public function getById(int $id): ProductNickname
    {
        return $this->repository->getById($id);
    }

    public function create(ProductNicknameDTO $dto): ProductNickname
    {
        return $this->repository->create($dto);
    }

    public function update(int $id, ProductNicknameDTO $dto): ProductNickname
    {
        return $this->repository->update($id, $dto);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}