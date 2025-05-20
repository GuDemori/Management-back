<?php

namespace Domain\Stock\Interfaces;

use Domain\Stock\DTOs\StockDTO;
use Illuminate\Support\Collection;

interface StockRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): StockDTO;
    public function create(StockDTO $data): StockDTO;
    public function update(int $id, StockDTO $data): StockDTO;
    public function delete(int $id): void;
}
