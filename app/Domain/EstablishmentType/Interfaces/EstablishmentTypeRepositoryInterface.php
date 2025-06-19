<?php

namespace App\Domain\EstablishmentType\Interfaces;

use App\Domain\EstablishmentType\DTOs\EstablishmentTypeDTO;

interface EstablishmentTypeRepositoryInterface
{
    /**
     * Retorna todos os tipos de estabelecimento.
     *
     * @return EstablishmentTypeDTO[]
     */
    public function all(): array;

    /**
     * Busca um tipo de estabelecimento por ID.
     *
     * @param  int  $id
     * @return EstablishmentTypeDTO|null
     */
    public function findById(int $id): ?EstablishmentTypeDTO;

    /**
     * Busca um tipo de estabelecimento pelo código.
     *
     * @param  string  $code
     * @return EstablishmentTypeDTO|null
     */
    public function findByCode(string $code): ?EstablishmentTypeDTO;

    /**
     * Persiste um novo tipo de estabelecimento.
     *
     * @param  EstablishmentTypeDTO  $dto
     * @return EstablishmentTypeDTO
     */
    public function create(EstablishmentTypeDTO $dto): EstablishmentTypeDTO;

    /**
     * Atualiza um tipo de estabelecimento existente.
     *
     * @param  EstablishmentTypeDTO  $dto
     * @return bool
     */
    public function update(EstablishmentTypeDTO $dto): bool;

    /**
     * Remove um tipo de estabelecimento pelo ID.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool;
}
