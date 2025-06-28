<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\ProductDTO;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function getAll(): Collection
    {
        return Product::with(['supplier', 'category', 'nicknames'])
            ->where('is_active', true)
            ->get();    }

    public function getById(int $id): Product
    {
        return Product::with(['supplier', 'category', 'nicknames'])->findOrFail($id);
    }

    public function searchByNickname(string $term): Collection
    {
        return Product::whereHas('nicknames', function ($q) use ($term) {
            $q->where('nickname', 'like', "%{$term}%");
        })->get();
    }

    public function create(ProductDTO $data): Product
    {
        if ($data->image instanceof UploadedFile) {
            $path = Storage::disk('s3')->putFile('products', $data->image);
            $data->image_url = Storage::disk('s3')->url($path);
        }

        return Product::create([
            'supplier_id'         => $data->supplier_id,
            'product_category_id' => $data->product_category_id,
            'name'                => $data->name,
            'description'         => $data->description,
            'image_url'           => $data->image_url,
            'costs'               => $data->costs,
            'wholesale_price'     => $data->wholesale_price,
            'retail_price'        => $data->retail_price,
        ]);
    }

    public function update(int $id, ProductDTO $data): Product
    {
        $product = Product::findOrFail($id);

        if ($data->image instanceof UploadedFile) {
            $path = Storage::disk('s3')->putFile('products', $data->image);
            $data->image_url = Storage::disk('s3')->url($path);
        }

        $product->update([
            'supplier_id'         => $data->supplier_id,
            'product_category_id' => $data->product_category_id,
            'name'                => $data->name,
            'description'         => $data->description,
            'image_url'           => $data->image_url,
            'costs'               => $data->costs,
            'wholesale_price'     => $data->wholesale_price,
            'retail_price'        => $data->retail_price,
        ]);

        return $product;
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => false]);
    }

}