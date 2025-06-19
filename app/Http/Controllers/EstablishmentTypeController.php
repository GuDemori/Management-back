<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEstablishmentTypeRequest;
use App\Http\Requests\UpdateEstablishmentTypeRequest;
use App\Domain\EstablishmentType\Interfaces\EstablishmentTypeServiceInterface;
use App\Domain\EstablishmentType\DTOs\EstablishmentTypeDTO;
use Illuminate\Http\JsonResponse;
use DomainException;

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
        $type = $this->service->findById($id);
        if (! $type) {
            return response()->json([
                'message' => 'Tipo de estabelecimento não encontrado.'
            ], 404);
        }
        return response()->json($type->toArray());
    }

    public function store(StoreEstablishmentTypeRequest $request): JsonResponse
    {
        $dto = new EstablishmentTypeDTO(
            id:   0,
            name: $request->name,
            code: $request->code
        );

        $created = $this->service->create($dto);
        return response()->json($created->toArray(), 201);
    }

    public function update(UpdateEstablishmentTypeRequest $request, int $id): JsonResponse
    {
        $dto = new EstablishmentTypeDTO(
            id:   $id,
            name: $request->name,
            code: $request->code
        );

        try {
            $this->service->update($dto);
            return response()->json([
                'message' => 'Tipo de estabelecimento atualizado com sucesso.'
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return response()->json(null, 204);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }
}