<?php

namespace App\Repositories;

use App\Domain\EstablishmentType\DTOs\EstablishmentTypeDTO;
use App\Domain\EstablishmentType\Interfaces\EstablishmentTypeRepositoryInterface;
use App\Models\EstablishmentType;

class EstablishmentTypeRepository implements EstablishmentTypeRepositoryInterface
{

    public function all(): array
    {
        return EstablishmentType::all()
            ->map(fn(EstablishmentType $model) => EstablishmentTypeDTO::fromModel($model))
            ->toArray();
    }

    public function findById(int $id): ?EstablishmentTypeDTO
    {
        $model = EstablishmentType::find($id);
        return $model ? EstablishmentTypeDTO::fromModel($model) : null;
    }

    public function findByCode(string $code): ?EstablishmentTypeDTO
    {
        $model = EstablishmentType::where('code', $code)->first();
        return $model ? EstablishmentTypeDTO::fromModel($model) : null;
    }

    public function create(EstablishmentTypeDTO $dto): EstablishmentTypeDTO
    {
        $model = EstablishmentType::create([
            'name' => $dto->name,
            'code' => $dto->code,
        ]);

        return EstablishmentTypeDTO::fromModel($model);
    }

    public function update(EstablishmentTypeDTO $dto): bool
    {
        $model = EstablishmentType::find($dto->id);

        if (! $model) {
            return false;
        }

        return $model->update([
            'name' => $dto->name,
            'code' => $dto->code,
        ]);
    }

    public function delete(int $id): bool
    {
        $model = EstablishmentType::find($id);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }
}
