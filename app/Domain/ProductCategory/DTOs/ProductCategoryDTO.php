<?php

namespace App\Domain\ProductCategory\DTOs;

use App\Models\ProductCategory;
use DateTimeImmutable;

class ProductCategoryDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $code,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null,
    ) {}

    public static function fromModel(ProductCategory $model): self
    {
        return new self(
            id:         $model->id,
            name:       $model->name,
            code:       $model->code,
            createdAt:  new DateTimeImmutable($model->created_at),
            updatedAt:  new DateTimeImmutable($model->updated_at),
        );
    }
}
