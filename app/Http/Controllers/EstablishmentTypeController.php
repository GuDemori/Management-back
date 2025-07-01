<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEstablishmentTypeRequest;
use App\Http\Requests\UpdateEstablishmentTypeRequest;
use App\Domain\EstablishmentType\Interfaces\EstablishmentTypeServiceInterface;
use App\Domain\EstablishmentType\DTOs\EstablishmentTypeDTO;
use Illuminate\Http\JsonResponse;
use DomainException;
use Illuminate\Support\Facades\Log;
use Throwable;

class EstablishmentTypeController extends Controller
{
    public function __construct(
        private EstablishmentTypeServiceInterface $service
    ) {}

    public function index(): JsonResponse
    {
        $types = $this->service->all();
        return response()->json($types);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $type = $this->service->findById($id);

            if (! $type) {
                Log::warning('[TipoEstabelecimento] Tipo não encontrado', ['id' => $id]);
                return response()->json([
                    'message' => 'Tipo de estabelecimento não encontrado.'
                ], 404);
            }

            Log::info('[TipoEstabelecimento] Tipo retornado com sucesso', ['id' => $id]);

            return response()->json($type->toArray());
        } catch (Throwable $e) {
            Log::error('[TipoEstabelecimento] Erro ao buscar tipo', [
                'id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erro ao buscar tipo de estabelecimento.'
            ], 500);
        }
    }

    public function store(StoreEstablishmentTypeRequest $request): JsonResponse
    {
        try {
            Log::info('[TipoEstabelecimento] Iniciando criação', [
                'user_id' => auth()->id(),
                'name'    => $request->name,
                'code'    => $request->code
            ]);

            $dto = new EstablishmentTypeDTO(
                id: 0,
                name: $request->name,
                code: $request->code
            );

            $created = $this->service->create($dto);

            Log::info('[TipoEstabelecimento] Criado com sucesso', [
                'id'   => $created->id,
                'name' => $created->name,
                'code' => $created->code,
                'user_id' => auth()->id()
            ]);

            return response()->json($created->toArray(), 201);
        } catch (Throwable $e) {
            Log::error('[TipoEstabelecimento] Erro ao criar tipo', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erro ao criar tipo de estabelecimento.'
            ], 500);
        }
    }

    public function update(UpdateEstablishmentTypeRequest $request, int $id): JsonResponse
    {
        $dto = new EstablishmentTypeDTO(
            id: $id,
            name: $request->name,
            code: $request->code
        );

        try {
            Log::info('[TipoEstabelecimento] Iniciando atualização', [
                'id' => $id,
                'user_id' => auth()->id()
            ]);

            $this->service->update($dto);

            Log::info('[TipoEstabelecimento] Atualizado com sucesso', [
                'id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'message' => 'Tipo de estabelecimento atualizado com sucesso.'
            ]);
        } catch (DomainException $e) {
            Log::warning('[TipoEstabelecimento] Não encontrado para atualização', [
                'id' => $id,
                'erro' => $e->getMessage()
            ]);

            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        } catch (Throwable $e) {
            Log::error('[TipoEstabelecimento] Erro ao atualizar tipo', [
                'id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erro ao atualizar tipo de estabelecimento.'
            ], 500);
        }
    }


    public function destroy(int $id): JsonResponse
    {
        try {
            Log::info('[TipoEstabelecimento] Iniciando exclusão', [
                'id' => $id,
                'user_id' => auth()->id()
            ]);

            $this->service->delete($id);

            Log::info('[TipoEstabelecimento] Excluído com sucesso', [
                'id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json(null, 204);
        } catch (DomainException $e) {
            Log::warning('[TipoEstabelecimento] Não encontrado para exclusão', [
                'id' => $id,
                'erro' => $e->getMessage()
            ]);

            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        } catch (Throwable $e) {
            Log::error('[TipoEstabelecimento] Erro ao excluir tipo', [
                'id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erro ao excluir tipo de estabelecimento.'
            ], 500);
        }
    }
}