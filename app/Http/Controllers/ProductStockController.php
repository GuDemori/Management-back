<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Domain\ProductStock\DTOs\ProductStockDTO;
use Domain\ProductStock\Interfaces\ProductStockServiceInterface;

class ProductStockController extends Controller
{
    public function __construct(
        private ProductStockServiceInterface $service
    ) {}

    public function index()
    {
        return response()->json($this->service->all());
    }

    public function show($productId, $stockId)
    {
        return response()->json($this->service->find($productId, $stockId));
    }

    public function store(Request $request)
    {
        $dto = ProductStockDTO::fromArray($this->validateData($request));
        $created = $this->service->create($dto);

        return response()->json($created, 201);
    }

    public function update(Request $request, $productId, $stockId)
    {
        $data = $this->validateData($request);

        $dto = ProductStockDTO::fromArray([
            ...$data,
            'product_id' => $productId,
            'stock_id'   => $stockId,
        ]);

        return response()->json($this->service->update($dto));
    }

    public function destroy($productId, $stockId)
    {
        $this->service->delete($productId, $stockId);
        return response()->noContent();
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'quantity'   => 'required|integer|min:0',
            'min_stock'  => 'nullable|integer|min:0',
            'isActive'   => 'boolean',
        ]);
    }
}