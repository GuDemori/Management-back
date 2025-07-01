<?php

namespace App\Http\Controllers;

use App\Domain\Product\Interfaces\ProductNicknameServiceInterface;
use App\Domain\Product\DTOs\ProductNicknameDTO;
use App\Http\Requests\StoreProductNicknameRequest;
use App\Http\Requests\UpdateProductNicknameRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductNicknameController extends Controller
{
    public function __construct(
        protected ProductNicknameServiceInterface $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            Log::info('[ApelidoProduto] Iniciando listagem de apelidos', [
                'user_id' => auth()->id()
            ]);

            $nicknames = $this->service->getAll();

            Log::info('[ApelidoProduto] Listagem concluída', [
                'total' => count($nicknames)
            ]);

            return response()->json($nicknames);
        } catch (Throwable $e) {
            Log::error('[ApelidoProduto] Erro ao listar apelidos', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao listar apelidos.'], 500);
        }
    }

    public function getByProduct(int $productId): JsonResponse
    {
        try {
            Log::info('[ApelidoProduto] Buscando apelidos por produto', [
                'product_id' => $productId,
                'user_id' => auth()->id()
            ]);

            $nicknames = $this->service->getByProduct($productId);

            Log::info('[ApelidoProduto] Apelidos retornados com sucesso', [
                'total' => count($nicknames)
            ]);

            return response()->json($nicknames);
        } catch (Throwable $e) {
            Log::error('[ApelidoProduto] Erro ao buscar apelidos por produto', [
                'product_id' => $productId,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao buscar apelidos do produto.'], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            Log::info('[ApelidoProduto] Iniciando busca por apelido', [
                'nickname_id' => $id,
                'user_id' => auth()->id()
            ]);

            $nickname = $this->service->getById($id);

            if (! $nickname) {
                Log::warning('[ApelidoProduto] Apelido não encontrado', ['nickname_id' => $id]);
                return response()->json(['message' => 'Apelido não encontrado.'], 404);
            }

            Log::info('[ApelidoProduto] Apelido retornado com sucesso', ['nickname_id' => $id]);

            return response()->json($nickname);
        } catch (Throwable $e) {
            Log::error('[ApelidoProduto] Erro ao buscar apelido', [
                'nickname_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao buscar apelido.'], 500);
        }
    }

    public function store(StoreProductNicknameRequest $request): JsonResponse
    {
        try {
            Log::info('[ApelidoProduto] Iniciando criação de apelido', [
                'user_id' => auth()->id(),
                'payload' => $request->validated()
            ]);

            $dto = ProductNicknameDTO::fromArray($request->validated());
            $nickname = $this->service->create($dto);

            Log::info('[ApelidoProduto] Apelido criado com sucesso', [
                'nickname_id' => $nickname->id,
                'product_id' => $nickname->product_id
            ]);

            return response()->json($nickname, Response::HTTP_CREATED);
        } catch (Throwable $e) {
            Log::error('[ApelidoProduto] Erro ao criar apelido', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Erro ao criar apelido.'], 500);
        }
    }

    public function update(UpdateProductNicknameRequest $request, int $id): JsonResponse
    {
        try {
            Log::info('[ApelidoProduto] Iniciando atualização de apelido', [
                'nickname_id' => $id,
                'user_id' => auth()->id(),
                'payload' => $request->validated()
            ]);

            $dto = ProductNicknameDTO::fromArray($request->validated());
            $nickname = $this->service->update($id, $dto);

            Log::info('[ApelidoProduto] Apelido atualizado com sucesso', [
                'nickname_id' => $id,
                'product_id' => $nickname->product_id ?? null
            ]);

            return response()->json($nickname);
        } catch (Throwable $e) {
            Log::error('[ApelidoProduto] Erro ao atualizar apelido', [
                'nickname_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Erro ao atualizar apelido.'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Log::info('[ApelidoProduto] Iniciando remoção de apelido', [
                'nickname_id' => $id,
                'user_id' => auth()->id()
            ]);

            $this->service->delete($id);

            Log::info('[ApelidoProduto] Apelido removido com sucesso', [
                'nickname_id' => $id
            ]);

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Throwable $e) {
            Log::error('[ApelidoProduto] Erro ao remover apelido', [
                'nickname_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json(['message' => 'Erro ao remover apelido.'], 500);
        }
    }
}
