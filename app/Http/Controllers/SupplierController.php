<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
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

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $dto = new SupplierDTO(
            name: $request->input('name'),
            companyName: $request->input('company_name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            document: $request->input('document'),
            city: $request->input('city'),
        );

        $supplier = $this->service->create($dto);

        return response()->json($supplier, 201);
    }


    public function update(UpdateSupplierRequest $request, int $id): JsonResponse
    {
        $dto = new SupplierDTO(
            name: $request->input('name'),
            companyName: $request->input('company_name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            document: $request->input('document'),
            city: $request->input('city'),
        );

        $supplier = $this->service->update($id, $dto);

        return response()->json($supplier);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Fornecedor removido com sucesso.']);
    }
}