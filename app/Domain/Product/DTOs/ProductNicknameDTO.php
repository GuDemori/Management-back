<?php

namespace App\Domain\Product\DTOs;

class ProductNicknameDTO
{
    public function __construct(
        public int $product_id,
        public string $nickname,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: (int) $data['product_id'],
            nickname: $data['nickname'],
        );
    }
}
