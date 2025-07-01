<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Domain\ProductStock\DTOs\ProductStockDTO;
use Domain\ProductStock\Interfaces\ProductStockServiceInterface;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductStockController extends Controller
{
    public function __construct(
        private ProductStockServiceInterface $service
    ) {}

    public function index()
    {
        Log::info('[ProdutoEstoque] Listando todos os produtos em estoque', [
            'user_id' => auth()->id()
        ]);
    
        return response()->json($this->service->all());
    }
    
    public function show($productId, $stockId)
    {
        Log::info('[ProdutoEstoque] Buscando item de estoque', [
            'product_id' => $productId,
            'stock_id' => $stockId,
            'user_id' => auth()->id()
        ]);
    
        return response()->json($this->service->find($productId, $stockId));
    }
    

    public function store(Request $request)
    {
        try {
            Log::info('[ProdutoEstoque] Iniciando criação de item em estoque', [
                'user_id' => auth()->id(),
                'payload' => $request->all()
            ]);
    
            $data = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'stock_id'   => 'required|integer|exists:stocks,id',
                'quantity'   => 'required|integer|min:0',
                'min_stock'  => 'nullable|integer|min:0',
                'isActive'   => 'boolean',
            ]);
    
            $dto     = ProductStockDTO::fromArray($data);
            $created = $this->service->create($dto);
    
            Log::info('[ProdutoEstoque] Item criado com sucesso', [
                'product_id' => $created->product_id ?? null,
                'stock_id'   => $created->stock_id ?? null
            ]);
    
            return response()->json($created, 201);
        } catch (Throwable $e) {
            Log::error('[ProdutoEstoque] Erro ao criar item', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json(['message' => 'Erro ao criar item em estoque.'], 500);
        }
    }

    public function update(Request $request, $productId, $stockId)
    {
        try {
            Log::info('[ProdutoEstoque] Iniciando atualização de item', [
                'product_id' => $productId,
                'stock_id'   => $stockId,
                'user_id'    => auth()->id(),
                'payload'    => $request->all()
            ]);
    
            $data = $this->validateData($request);
    
            $dto = ProductStockDTO::fromArray([
                ...$data,
                'product_id' => $productId,
                'stock_id'   => $stockId,
            ]);
    
            $updated = $this->service->update($dto);
    
            Log::info('[ProdutoEstoque] Item atualizado com sucesso', [
                'product_id' => $productId,
                'stock_id'   => $stockId
            ]);
    
            return response()->json($updated);
        } catch (Throwable $e) {
            Log::error('[ProdutoEstoque] Erro ao atualizar item', [
                'product_id' => $productId,
                'stock_id'   => $stockId,
                'erro'       => $e->getMessage(),
                'trace'      => $e->getTraceAsString()
            ]);
    
            return response()->json(['message' => 'Erro ao atualizar item em estoque.'], 500);
        }
    }

    public function destroy($productId, $stockId)
    {
        try {
            Log::info('[ProdutoEstoque] Iniciando exclusão de item', [
                'product_id' => $productId,
                'stock_id'   => $stockId,
                'user_id'    => auth()->id()
            ]);
    
            $this->service->delete($productId, $stockId);
    
            Log::info('[ProdutoEstoque] Item excluído com sucesso', [
                'product_id' => $productId,
                'stock_id'   => $stockId
            ]);
    
            return response()->noContent();
        } catch (Throwable $e) {
            Log::error('[ProdutoEstoque] Erro ao excluir item', [
                'product_id' => $productId,
                'stock_id'   => $stockId,
                'erro'       => $e->getMessage(),
                'trace'      => $e->getTraceAsString()
            ]);
    
            return response()->json(['message' => 'Erro ao excluir item em estoque.'], 500);
        }
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