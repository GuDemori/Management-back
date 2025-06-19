<?php

namespace App\Http\Controllers;

use App\Domain\Product\DTOs\ProductDTO;
use App\Domain\Product\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
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
        return response()->json($product, Response::HTTP_CREATED);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $dto = ProductDTO::fromArray($request->validated());
        $product = $this->productService->update($id, $dto);
        return response()->json($product);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
