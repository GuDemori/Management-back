<?php

namespace App\Domain\Product\DTOs;

use Illuminate\Http\UploadedFile;

class ProductDTO
{
    public function __construct(
        public int $supplier_id,
        public int $product_category_id,
        public string $name,
        public ?string $description,
        public ?string $image_url,
        public float $costs,
        public float $wholesale_price,
        public float $retail_price,
        public ?UploadedFile $image,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            supplier_id: (int) $data['supplier_id'],
            product_category_id: (int) $data['product_category_id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            image_url: $data['image_url'] ?? null,
            costs: (float) $data['costs'],
            wholesale_price: (float) $data['wholesale_price'],
            retail_price: (float) $data['retail_price'],
            image: $data['image'] ?? null,
        );
    }
}
