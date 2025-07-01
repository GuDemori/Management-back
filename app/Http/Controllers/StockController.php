<?php

namespace App\Http\Controllers;

use App\Services\CepLookupService;
use Illuminate\Http\Request;
use Domain\Stock\DTOs\StockDTO;
use Domain\Stock\Interfaces\StockServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class StockController extends Controller
{
    public function __construct(
        private StockServiceInterface $stockService,
        private CepLookupService $cepService
    ) {}

    public function index()
    {
        try {
            Log::info('[Estoque] Iniciando listagem de estoques', ['user_id' => auth()->id()]);

            $data = $this->stockService->all();

            Log::info('[Estoque] Listagem concluída', ['total' => count($data)]);

            return response()->json($data);
        } catch (Throwable $e) {
            Log::error('[Estoque] Erro ao listar estoques', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao listar estoques.'], 500);
        }
    }

    public function show($id)
    {
        try {
            Log::info('[Estoque] Iniciando busca por estoque', [
                'stock_id' => $id,
                'user_id' => auth()->id()
            ]);

            $stock = $this->stockService->find($id);

            if (!$stock) {
                Log::warning('[Estoque] Estoque não encontrado', ['stock_id' => $id]);
                return response()->json(['message' => 'Estoque não encontrado.'], 404);
            }

            Log::info('[Estoque] Estoque retornado com sucesso', ['stock_id' => $id]);

            return response()->json($stock);
        } catch (Throwable $e) {
            Log::error('[Estoque] Erro ao buscar estoque', [
                'stock_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao buscar estoque.'], 500);
        }
    }


    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'cep'    => 'required|string|size:9',
                'number' => 'required|string|max:10',
            ]);

            Log::info('[Estoque] Iniciando criação de estoque', [
                'user_id' => auth()->id(),
                'cep'     => $data['cep']
            ]);

            $cepData = $this->cepService->buscarEnderecoPorCep($data['cep']);

            if (! $cepData) {
                Log::warning('[Estoque] CEP não encontrado', ['cep' => $data['cep']]);
                return response()->json(['cep' => 'CEP não encontrado'], 422);
            }

            $logradouro         = $cepData['address'] ?? '';
            $data['address']    = $request->input('address', $logradouro);
            $data['city']       = $cepData['city'];
            $data['state']      = $cepData['state'];

            Validator::make($data, [
                'address' => 'required|string|max:255',
                'city'    => 'required|string|max:100',
                'state'   => 'required|string|max:100',
            ])->validate();

            $dto   = StockDTO::fromArray($data);
            $stock = $this->stockService->create($dto);

            Log::info('[Estoque] Estoque criado com sucesso', [
                'stock_id' => $stock->id ?? null,
                'user_id'  => auth()->id()
            ]);

            return response()->json($stock, 201);
        } catch (Throwable $e) {
            Log::error('[Estoque] Erro ao criar estoque', [
                'user_id' => auth()->id(),
                'erro'    => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao criar estoque.'], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            Log::info('[Estoque] Iniciando atualização de estoque', [
                'stock_id' => $id,
                'user_id'  => auth()->id()
            ]);

            $data = $this->validateData($request);
            $dto  = StockDTO::fromArray($data);

            $updatedStock = $this->stockService->update($id, $dto);

            Log::info('[Estoque] Estoque atualizado com sucesso', [
                'stock_id' => $id,
                'user_id'  => auth()->id()
            ]);

            return response()->json($updatedStock);
        } catch (Throwable $e) {
            Log::error('[Estoque] Erro ao atualizar estoque', [
                'stock_id' => $id,
                'user_id'  => auth()->id(),
                'erro'     => $e->getMessage(),
                'trace'    => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao atualizar estoque.'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Log::info('[Estoque] Iniciando exclusão de estoque', [
                'stock_id' => $id,
                'user_id'  => auth()->id()
            ]);

            $this->stockService->delete($id);

            Log::info('[Estoque] Estoque excluído com sucesso', [
                'stock_id' => $id,
                'user_id'  => auth()->id()
            ]);

            return response()->json(null, 204);
        } catch (Throwable $e) {
            Log::error('[Estoque] Erro ao excluir estoque', [
                'stock_id' => $id,
                'user_id'  => auth()->id(),
                'erro'     => $e->getMessage(),
                'trace'    => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao excluir estoque.'], 500);
        }
    }

    private function validateData(Request $request): array
    {

        $data = $request->validate([
            'cep'      => 'required|string|size:9',
            'address'  => 'nullable|string|max:255',
            'number'   => 'nullable|string|max:10',
            'city'     => 'nullable|string|max:100',
            'state'    => 'nullable|string|max:100',
            'isActive' => 'boolean',
        ]);

        return $data;
    }

    public function deactivate(int $id)
    {
        try {
            Log::info('[Estoque] Iniciando desativação de estoque', ['stock_id' => $id]);

            $stock = $this->stockService->find($id);

            if (! $stock) {
                Log::warning('[Estoque] Estoque não encontrado para desativação', ['stock_id' => $id]);
                return response()->json(['message' => 'Estoque não encontrado.'], 404);
            }

            $dto = new StockDTO(
                id: $stock->id,
                cep: $stock->cep,
                address: $stock->address,
                number: $stock->number,
                city: $stock->city,
                state: $stock->state,
                isActive: false
            );

            $updated = $this->stockService->update($id, $dto);

            Log::info('[Estoque] Estoque desativado com sucesso', ['stock_id' => $id]);
            return response()->json($updated);
        } catch (Throwable $e) {
            Log::error('[Estoque] Erro ao desativar estoque', [
                'stock_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Erro interno ao desativar o estoque.'], 500);
        }
    }
}
