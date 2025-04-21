<?php

namespace Domain\Supplier\DTOs;

class SupplierDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $phone,
    ) {}
}
