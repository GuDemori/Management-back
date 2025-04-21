<?php

namespace App\Services;

use App\Models\Product;
use App\DTOs\ProductDTO;
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

    public function create(ProductDTO $data): Product
    {
        return Product::create([
            'name'         => $data->name,
            'description'  => $data->description,
            'price'        => $data->price,
            'stock_id'     => $data->stock_id,
            'supplier_id'  => $data->supplier_id,
        ]);
    }

    public function update(int $id, ProductDTO $data): Product
    {
        $product = Product::findOrFail($id);

        $product->update([
            'name'         => $data->name,
            'description'  => $data->description,
            'price'        => $data->price,
            'stock_id'     => $data->stock_id,
            'supplier_id'  => $data->supplier_id,
        ]);

        return $product;
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }
}
