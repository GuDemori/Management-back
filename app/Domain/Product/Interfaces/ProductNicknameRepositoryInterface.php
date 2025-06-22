<?php

namespace App\Domain\Product\Interfaces;

use App\Domain\Product\DTOs\ProductNicknameDTO;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ProductNickname;

interface ProductNicknameRepositoryInterface
{
    public function getAll(): Collection;

    public function getByProduct(int $productId): Collection;

    public function getById(int $id): ProductNickname;

    public function create(ProductNicknameDTO $dto): ProductNickname;

    public function update(int $id, ProductNicknameDTO $dto): ProductNickname;

    public function delete(int $id): void;
}