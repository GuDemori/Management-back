<?php

namespace Domain\Stock\Services;

use App\Services\CepLookupService;
use Illuminate\Support\Collection;
use Domain\Stock\DTOs\StockDTO;
use Domain\Stock\Interfaces\StockServiceInterface;
use Domain\Stock\Interfaces\StockRepositoryInterface;

class StockService implements StockServiceInterface
{
    public function __construct(
        private StockRepositoryInterface $repository,
        private CepLookupService $cepService
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): StockDTO
    {
        return $this->repository->find($id);
    }

    public function create(StockDTO $data): StockDTO
    {
        if (empty($data->address) || empty($data->city) || empty($data->state)) {
            $viacep = app(\App\Services\CepLookupService::class)->buscarEnderecoPorCep($data->cep);

            if (!$viacep) {
                throw new \InvalidArgumentException("CEP inválido ou não encontrado.");
            }

            $data = new StockDTO(
                id: $data->id,
                cep: $data->cep,
                address: $viacep['address'] ?? '',
                number: $data->number,
                city: $viacep['city'] ?? '',
                state: $viacep['state'] ?? '',
                isActive: $data->isActive
            );
        }

        return $this->repository->create($data);
    }

    public function update(int $id, StockDTO $data): StockDTO
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}