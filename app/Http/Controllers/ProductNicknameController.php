<?php

namespace App\Http\Controllers;

use App\Domain\Product\Interfaces\ProductNicknameServiceInterface;
use App\Domain\Product\DTOs\ProductNicknameDTO;
use App\Http\Requests\StoreProductNicknameRequest;
use App\Http\Requests\UpdateProductNicknameRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductNicknameController extends Controller
{
    public function __construct(
        protected ProductNicknameServiceInterface $service
    ) {}

    public function index(): JsonResponse
    {
        $nicknames = $this->service->getAll();
        return response()->json($nicknames);
    }

    public function getByProduct(int $productId): JsonResponse
    {
        $nicknames = $this->service->getByProduct($productId);
        return response()->json($nicknames);
    }

    public function show(int $id): JsonResponse
    {
        $nickname = $this->service->getById($id);
        return response()->json($nickname);
    }

    public function store(StoreProductNicknameRequest $request): JsonResponse
    {
        $dto = ProductNicknameDTO::fromArray($request->validated());
        $nickname = $this->service->create($dto);
        return response()->json($nickname, Response::HTTP_CREATED);
    }

    public function update(UpdateProductNicknameRequest $request, int $id): JsonResponse
    {
        $dto = ProductNicknameDTO::fromArray($request->validated());
        $nickname = $this->service->update($id, $dto);
        return response()->json($nickname);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
