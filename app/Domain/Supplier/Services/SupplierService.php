<?php

namespace Domain\Supplier\Services;

use App\Models\Supplier;
use Domain\Supplier\DTOs\SupplierDTO;
use Domain\Supplier\Interfaces\SupplierServiceInterface;
use Domain\Supplier\Interfaces\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SupplierService implements SupplierServiceInterface
{
    public function __construct(
        private readonly SupplierRepositoryInterface $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function findById(int $id): ?Supplier
    {
        return $this->repository->find($id);
    }

    public function create(SupplierDTO $data): Supplier
    {
        return $this->repository->create([
            'name'  => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
        ]);
    }

    public function update(int $id, SupplierDTO $data): Supplier
    {
        $supplier = $this->repository->find($id);

        if (!$supplier) {
            throw new \Exception('Fornecedor não encontrado.');
        }

        return $this->repository->update($supplier, [
            'name'  => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
        ]);
    }

    public function delete(int $id): void
    {
        $supplier = $this->repository->find($id);

        if (!$supplier) {
            throw new \Exception('Fornecedor não encontrado.');
        }

        $this->repository->delete($supplier);
    }
}
