<?php

namespace App\Http\Controllers;

use App\Domain\ProductCategory\DTOs\ProductCategoryDTO;
use App\Domain\ProductCategory\Interfaces\ProductCategoryServiceInterface;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    private ProductCategoryServiceInterface $service;

    public function __construct(ProductCategoryServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $categories = $this->service->all();
        return response()->json($categories);
    }

    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        $dto = new ProductCategoryDTO(
            id: null,
            name: $request->validated()['name'],
            code: $request->validated()['code']
        );

        $created = $this->service->create($dto);
        return response()->json($created, 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->service->findById($id);
        if (! $category) {
            return response()->json(['message' => 'Product category not found'], 404);
        }
        return response()->json($category);
    }

    public function update(UpdateProductCategoryRequest $request, int $id): JsonResponse
    {
        $dto = new ProductCategoryDTO(
            id: $id,
            name: $request->validated()['name'],
            code: $request->validated()['code']
        );

        $success = $this->service->update($dto);
        if (! $success) {
            return response()->json(['message' => 'Unable to update product category'], 400);
        }

        return response()->json(['message' => 'Product category updated successfully']);
    }

    public function destroy(int $id): JsonResponse
    {
        $success = $this->service->delete($id);
        if (! $success) {
            return response()->json(['message' => 'Unable to delete product category'], 400);
        }

        return response()->json(null, 204);
    }
}
