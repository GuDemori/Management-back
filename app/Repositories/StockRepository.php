<?php

namespace App\Repositories;

use App\Models\Stock;
use Illuminate\Support\Collection;
use Domain\Stock\DTOs\StockDTO;
use Domain\Stock\Interfaces\StockRepositoryInterface;
use App\Services\CepLookupService;

class StockRepository implements StockRepositoryInterface
{
    public function __construct(
        private CepLookupService $cepService
    ) {}

    public function all(): Collection
    {
        return Stock::all()->map(fn($stock) => $this->mapToDTO($stock));
    }

    public function find(int $id): StockDTO
    {
        $stock = Stock::findOrFail($id);
        return $this->mapToDTO($stock);
    }

    public function create(StockDTO $data): StockDTO
    {
        $dados = $this->fillMissingAddressData($data);

        $stock = Stock::create([
            'cep'      => $dados->cep,
            'address'  => $dados->address,
            'number'   => $dados->number,
            'city'     => $dados->city,
            'state'    => $dados->state,
            'isActive' => $dados->isActive,
        ]);

        return $this->mapToDTO($stock);
    }

    public function update(int $id, StockDTO $data): StockDTO
    {
        $stock = Stock::findOrFail($id);
        $dados = $this->fillMissingAddressData($data);

        $stock->update([
            'cep'      => $dados->cep,
            'address'  => $dados->address,
            'number'   => $dados->number,
            'city'     => $dados->city,
            'state'    => $dados->state,
            'isActive' => $dados->isActive,
        ]);

        return $this->mapToDTO($stock);
    }

    public function delete(int $id): void
    {
        Stock::destroy($id);
    }

    private function fillMissingAddressData(StockDTO $dto): StockDTO
    {
        if ($dto->address && $dto->city && $dto->state) {
            return $dto;
        }

        $dadosViaCep = $this->cepService->buscarEnderecoPorCep($dto->cep);

        return new StockDTO(
            id: $dto->id,
            cep: $dto->cep,
            address: $dto->address ?? $dadosViaCep['address'] ?? null,
            number: $dto->number,
            city: $dto->city ?? $dadosViaCep['city'] ?? null,
            state: $dto->state ?? $dadosViaCep['state'] ?? null,
            isActive: $dto->isActive,
        );
    }

    private function mapToDTO(Stock $stock): StockDTO
    {
        return new StockDTO(
            id: $stock->id,
            cep: $stock->cep       ?? '',
            address: $stock->address ?? '',
            number: $stock->number ?? '',
            city: $stock->city ?? '',
            state: $stock->state ?? '',
            isActive: $stock->isActive ?? '',
        );
    }
}