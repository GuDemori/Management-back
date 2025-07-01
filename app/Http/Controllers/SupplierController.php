<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Domain\Supplier\Interfaces\SupplierServiceInterface;
use Domain\Supplier\DTOs\SupplierDTO;
use Illuminate\Support\Facades\Log;
use Throwable;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierServiceInterface $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            Log::info('[Produto] Iniciando listagem de produtos', ['user_id' => auth()->id()]);

            $products = $this->service->getAll();

            Log::info('[Produto] Listagem concluída', ['total' => count($products)]);

            return response()->json($products);
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao listar produtos', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Erro ao listar produtos.'], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            Log::info('[Produto] Iniciando busca por produto', [
                'product_id' => $id,
                'user_id' => auth()->id()
            ]);

            $product = $this->service->findById($id);

            if (!$product) {
                Log::warning('[Produto] Produto não encontrado', ['product_id' => $id]);
                return response()->json(['message' => 'Produto não encontrado.'], 404);
            }

            Log::info('[Produto] Produto encontrado com sucesso', ['product_id' => $id]);

            return response()->json($product);
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao buscar produto', [
                'product_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Erro ao buscar produto.'], 500);
        }
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        try {
            Log::info('[Fornecedor] Iniciando criação de fornecedor', [
                'name' => $request->input('name'),
                'user_id' => auth()->id()
            ]);

            $dto = new SupplierDTO(
                name: $request->input('name'),
                companyName: $request->input('company_name'),
                email: $request->input('email'),
                phone: $request->input('phone'),
                document: $request->input('document'),
                city: $request->input('city'),
            );

            $supplier = $this->service->create($dto);

            Log::info('[Fornecedor] Fornecedor criado com sucesso', [
                'supplier_id' => $supplier->id ?? null,
                'user_id' => auth()->id()
            ]);

            return response()->json($supplier, 201);
        } catch (Throwable $e) {
            Log::error('[Fornecedor] Erro ao criar fornecedor', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Erro ao criar fornecedor.'], 500);
        }
    }

    public function update(UpdateSupplierRequest $request, int $id): JsonResponse
    {
        try {
            Log::info('[Fornecedor] Iniciando atualização de fornecedor', [
                'supplier_id' => $id,
                'user_id' => auth()->id()
            ]);

            $dto = new SupplierDTO(
                name: $request->input('name'),
                companyName: $request->input('company_name'),
                email: $request->input('email'),
                phone: $request->input('phone'),
                document: $request->input('document'),
                city: $request->input('city'),
            );

            $supplier = $this->service->update($id, $dto);

            Log::info('[Fornecedor] Fornecedor atualizado com sucesso', [
                'supplier_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json($supplier);
        } catch (Throwable $e) {
            Log::error('[Fornecedor] Erro ao atualizar fornecedor', [
                'supplier_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Erro ao atualizar fornecedor.'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Log::info('[Fornecedor] Iniciando exclusão de fornecedor', [
                'supplier_id' => $id,
                'user_id' => auth()->id()
            ]);

            $this->service->delete($id);

            Log::info('[Fornecedor] Fornecedor excluído com sucesso', [
                'supplier_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Fornecedor removido com sucesso.']);
        } catch (Throwable $e) {
            Log::error('[Fornecedor] Erro ao excluir fornecedor', [
                'supplier_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Erro ao excluir fornecedor.'], 500);
        }
    }
}