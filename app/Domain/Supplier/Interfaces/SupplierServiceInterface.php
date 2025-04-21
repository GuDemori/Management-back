<?php

namespace Domain\Supplier\Interfaces;

use App\Models\Supplier;
use Domain\Supplier\DTOs\SupplierDTO;
use Illuminate\Database\Eloquent\Collection;

interface SupplierServiceInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Supplier;
    public function create(SupplierDTO $data): Supplier;
    public function update(int $id, SupplierDTO $data): Supplier;
    public function delete(int $id): void;
}
