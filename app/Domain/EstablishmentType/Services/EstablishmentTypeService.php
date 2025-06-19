<?php

namespace App\Domain\EstablishmentType\Services;

use App\Domain\EstablishmentType\DTOs\EstablishmentTypeDTO;
use App\Domain\EstablishmentType\Interfaces\EstablishmentTypeRepositoryInterface;
use App\Domain\EstablishmentType\Interfaces\EstablishmentTypeServiceInterface;
use DomainException;

class EstablishmentTypeService implements EstablishmentTypeServiceInterface
{
    public function __construct(
        private EstablishmentTypeRepositoryInterface $repository
    ) {}

    public function all(): array
    {
        return $this->repository->all();
    }

    public function findById(int $id): ?EstablishmentTypeDTO
    {
        return $this->repository->findById($id);
    }

    public function findByCode(string $code): ?EstablishmentTypeDTO
    {
        return $this->repository->findByCode($code);
    }

    public function create(EstablishmentTypeDTO $dto): EstablishmentTypeDTO
    {
        return $this->repository->create($dto);
    }

    public function update(EstablishmentTypeDTO $dto): bool
    {
        $existing = $this->repository->findById($dto->id);
        if (! $existing) {
            throw new DomainException("EstablishmentType with ID {$dto->id} not found.");
        }

        return $this->repository->update($dto);
    }

    public function delete(int $id): bool
    {
        $existing = $this->repository->findById($id);
        if (! $existing) {
            throw new DomainException("EstablishmentType with ID {$id} not found.");
        }

        return $this->repository->delete($id);
    }
}
