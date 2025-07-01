<?php

namespace App\Http\Controllers;

use App\Domain\ProductCategory\DTOs\ProductCategoryDTO;
use App\Domain\ProductCategory\Interfaces\ProductCategoryServiceInterface;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductCategoryController extends Controller
{
    private ProductCategoryServiceInterface $service;

    public function __construct(ProductCategoryServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        try {
            Log::info('[CategoriaProduto] Iniciando listagem', [
                'user_id' => auth()->id()
            ]);

            $categories = $this->service->all();

            Log::info('[CategoriaProduto] Listagem concluída', [
                'total' => count($categories)
            ]);

            return response()->json($categories);
        } catch (Throwable $e) {
            Log::error('[CategoriaProduto] Erro ao listar categorias', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erro ao listar categorias de produto.'
            ], 500);
        }
    }


    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            Log::info('[CategoriaProduto] Iniciando criação', [
                'user_id' => auth()->id(),
                'name'    => $validated['name'],
                'code'    => $validated['code']
            ]);

            $dto = new ProductCategoryDTO(
                id: null,
                name: $validated['name'],
                code: $validated['code']
            );

            $created = $this->service->create($dto);

            Log::info('[CategoriaProduto] Categoria criada com sucesso', [
                'category_id' => $created->id ?? null
            ]);

            return response()->json($created, 201);
        } catch (Throwable $e) {
            Log::error('[CategoriaProduto] Erro ao criar categoria', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erro ao criar categoria de produto.'
            ], 500);
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            Log::info('[CategoriaProduto] Iniciando busca por categoria', [
                'category_id' => $id,
                'user_id'     => auth()->id()
            ]);

            $category = $this->service->findById($id);

            if (! $category) {
                Log::warning('[CategoriaProduto] Categoria não encontrada', [
                    'category_id' => $id
                ]);

                return response()->json(['message' => 'Categoria de produto não encontrada'], 404);
            }

            Log::info('[CategoriaProduto] Categoria encontrada com sucesso', [
                'category_id' => $id
            ]);

            return response()->json($category);
        } catch (Throwable $e) {
            Log::error('[CategoriaProduto] Erro ao buscar categoria', [
                'category_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao buscar categoria de produto.'], 500);
        }
    }

    public function update(UpdateProductCategoryRequest $request, int $id): JsonResponse
    {
        try {
            Log::info('[CategoriaProduto] Iniciando atualização da categoria', [
                'category_id' => $id,
                'user_id'     => auth()->id()
            ]);

            $dto = new ProductCategoryDTO(
                id: $id,
                name: $request->validated()['name'],
                code: $request->validated()['code']
            );

            $success = $this->service->update($dto);

            if (! $success) {
                Log::warning('[CategoriaProduto] Falha ao atualizar categoria', [
                    'category_id' => $id
                ]);

                return response()->json(['message' => 'Não foi possível atualizar a categoria de produto'], 400);
            }

            Log::info('[CategoriaProduto] Categoria atualizada com sucesso', [
                'category_id' => $id
            ]);

            return response()->json(['message' => 'Categoria de produto atualizada com sucesso']);
        } catch (Throwable $e) {
            Log::error('[CategoriaProduto] Erro ao atualizar categoria', [
                'category_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao atualizar categoria de produto.'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Log::info('[CategoriaProduto] Iniciando remoção da categoria', [
                'category_id' => $id,
                'user_id'     => auth()->id()
            ]);

            $success = $this->service->delete($id);

            if (! $success) {
                Log::warning('[CategoriaProduto] Falha ao remover categoria', [
                    'category_id' => $id
                ]);

                return response()->json(['message' => 'Não foi possível remover a categoria de produto'], 400);
            }

            Log::info('[CategoriaProduto] Categoria removida com sucesso', [
                'category_id' => $id
            ]);

            return response()->json(null, 204);
        } catch (Throwable $e) {
            Log::error('[CategoriaProduto] Erro ao remover categoria', [
                'category_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao remover categoria de produto.'], 500);
        }
    }
}
