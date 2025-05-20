<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Domain\Supplier\Interfaces\SupplierServiceInterface;
use Domain\Supplier\DTOs\SupplierDTO;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierServiceInterface $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->service->findById($id));
    }

    public function store(Request $request): JsonResponse
    {
        $dto = new SupplierDTO(...$request->only(['name', 'email', 'phone']));
        return response()->json($this->service->create($dto), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $dto = new SupplierDTO(...$request->only(['name', 'email', 'phone']));
        return response()->json($this->service->update($id, $dto));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Fornecedor removido com sucesso.']);
    }
}
