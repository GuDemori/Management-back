<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Domain\Stock\DTOs\StockDTO;
use Domain\Stock\Interfaces\StockServiceInterface;

class StockController extends Controller
{
    public function __construct(
        private StockServiceInterface $stockService
    ) {}

    public function index()
    {
        return response()->json($this->stockService->all());
    }

    public function show($id)
    {
        return response()->json($this->stockService->find($id));
    }

    public function store(Request $request)
    {
        $dto = StockDTO::fromArray($request->validate([
            'location' => 'required|string|max:255',
        ]));
    
        $stock = $this->stockService->create($dto);
    
        return response()->json($stock, 201);
    }

    public function update(Request $request, $id)
    {
        $dto = StockDTO::fromArray($request->validate([
            'location' => 'required|string|max:255',
        ]));

        return response()->json($this->stockService->update($id, $dto));
    }

    public function destroy($id)
    {
        $this->stockService->delete($id);
        return response()->noContent();
    }
}