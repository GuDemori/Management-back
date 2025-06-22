<?php

namespace App\Repositories;

use App\Domain\Product\Interfaces\ProductNicknameRepositoryInterface;
use App\Domain\Product\DTOs\ProductNicknameDTO;
use App\Models\ProductNickname;
use Illuminate\Database\Eloquent\Collection;

class ProductNicknameRepository implements ProductNicknameRepositoryInterface
{
    public function getAll(): Collection
    {
        return ProductNickname::all();
    }

    public function getByProduct(int $productId): Collection
    {
        return ProductNickname::where('product_id', $productId)->get();
    }

    public function getById(int $id): ProductNickname
    {
        return ProductNickname::findOrFail($id);
    }

    public function create(ProductNicknameDTO $dto): ProductNickname
    {
        return ProductNickname::create([
            'product_id' => $dto->product_id,
            'nickname'   => $dto->nickname,
        ]);
    }

    public function update(int $id, ProductNicknameDTO $dto): ProductNickname
    {
        $nickname = ProductNickname::findOrFail($id);
        $nickname->update([
            'product_id' => $dto->product_id,
            'nickname'   => $dto->nickname,
        ]);

        return $nickname;
    }

    public function delete(int $id): void
    {
        $nickname = ProductNickname::findOrFail($id);
        $nickname->delete();
    }
}