<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\ProductDTO;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    public function getAll(): Collection
    {
        return Product::all();
    }

    public function getById(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function searchByNickname(string $term): Collection
    {
        return Product::whereHas('nicknames', function ($q) use ($term) {
            $q->where('nickname', 'like', "%{$term}%");
        })->get();
    }

    public function create(ProductDTO $data): Product
    {
        return Product::create([
            'supplier_id'        => $data->supplier_id,
            'product_category_id'=> $data->product_category_id,
            'name'               => $data->name,
            'description'        => $data->description,
            'image_url'          => $data->image_url,
            'costs'              => $data->costs,
            'wholesale_price'    => $data->wholesale_price,
            'retail_price'       => $data->retail_price,
        ]);
    }

    public function update(int $id, ProductDTO $data): Product
    {
        $product = Product::findOrFail($id);

        $product->update([
            'supplier_id'        => $data->supplier_id,
            'product_category_id'=> $data->product_category_id,
            'name'               => $data->name,
            'description'        => $data->description,
            'image_url'          => $data->image_url,
            'costs'              => $data->costs,
            'wholesale_price'    => $data->wholesale_price,
            'retail_price'       => $data->retail_price,
        ]);

        return $product;
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }
}
