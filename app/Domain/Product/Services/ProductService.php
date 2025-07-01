<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\ProductDTO;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductService
{
    public function getAll(?int $stockId = null): Collection
    {
        $query = Product::query()
            ->with(['supplier', 'category', 'nicknames'])
            ->where('is_active', true);

        if ($stockId) {
            $query->withSum(['productStocks as stock_quantity' => function ($q) use ($stockId) {
                $q->where('is_active', true)->where('stock_id', $stockId);
            }], 'quantity');

            $query->whereHas('productStocks', function ($q) use ($stockId) {
                $q->where('stock_id', $stockId)->where('is_active', true);
            });
        } else {
            $query->withSum(['productStocks as stock_quantity' => function ($q) {
                $q->where('is_active', true);
            }], 'quantity');
        }

        return $query->get();
    }



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
        try {
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
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao criar produto', [
                'nome'  => $data->name,
                'erro'  => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
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