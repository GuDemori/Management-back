<?php

namespace App\Domain\Product\Interfaces;

use App\Domain\Product\DTOs\ProductDTO;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    public function getAll(): Collection;
    public function getById(int $id);
    public function create(ProductDTO $data);
    public function update(int $id, ProductDTO $data);
    public function delete(int $id): void;
}