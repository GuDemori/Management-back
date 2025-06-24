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

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();
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
}
