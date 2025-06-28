<?php

namespace App\Http\Controllers;

use App\Domain\Product\DTOs\ProductDTO;
use App\Domain\Product\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $stockId = $request->query('stock_id');
        $products = $this->productService->getAll($stockId);

        return response()->json($products);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);
        return response()->json($product);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $dto = ProductDTO::fromArray($request->validated());
        $product = $this->productService->create($dto);
        if (isset($data['nicknames'])) {
            foreach ($data['nicknames'] as $nickname) {
                $product->nicknames()->create(['nickname' => $nickname]);
            }
        }
        return response()->json($product, Response::HTTP_CREATED);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $dto = ProductDTO::fromArray($request->validated());
        $product = $this->productService->update($id, $dto);
        if (isset($data['nicknames'])) {
            foreach ($data['nicknames'] as $nickname) {
                $product->nicknames()->create(['nickname' => $nickname]);
            }
        }
        return response()->json($product);
    }

    public function updateNicknames(Request $request, Product $product)
    {
        $request->validate([
            'nicknames'   => 'required|array',
            'nicknames.*' => 'required|string|max:255',
        ]);

        $product->nicknames()->delete();

        foreach ($request->nicknames as $nickname) {
            $product->nicknames()->create([
                'nickname' => $nickname,
            ]);
        }

        return response()->json(['message' => 'Apelidos atualizados.']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function searchByNickname(Request $request): JsonResponse
    {
        $term = $request->query('nickname', '');
        $products = $this->productService->searchByNickname($term);
        return response()->json($products);
    }

    public function removeOrphanImages(): void
    {
        $allProductImages = Product::pluck('image_url')->map(function ($url) {
            return ltrim(parse_url($url, PHP_URL_PATH), '/');
        })->toArray();
        $allImagesInS3 = Storage::disk('s3')->files('products');

        $orphans = array_diff($allImagesInS3, $allProductImages);

        foreach ($orphans as $imageUrl) {
            Storage::disk('s3')->delete($imageUrl);
            Log::info("Imagem órfã removida: {$imageUrl}");
        }
    }

    public function deleteImageFromS3(string $imageUrl): void
    {
        if (Storage::disk('s3')->exists($imageUrl)) {
            Storage::disk('s3')->delete($imageUrl);
        }
    }

    public function deactivate(Product $product): JsonResponse
    {
        if ($product->image_url) {
            $imagePath = ltrim(parse_url($product->image_url, PHP_URL_PATH), '/');

            Log::info("Tentando excluir imagem do S3: {$imagePath}");
            $this->deleteImageFromS3($imagePath);

            $product->image_url = null;
        }

        $product->is_active = false;
        $product->save();

        Log::info("Produto {$product->id} desativado");

        $this->removeOrphanImages();

        return response()->json(['message' => 'Produto desativado com sucesso.']);
    }


}