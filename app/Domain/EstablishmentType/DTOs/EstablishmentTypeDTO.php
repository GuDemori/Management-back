<?php

namespace App\Domain\EstablishmentType\DTOs;

use App\Models\EstablishmentType;

class EstablishmentTypeDTO
{
    public readonly int $id;
    public readonly string $name;
    public readonly string $code;

    public function __construct(int $id, string $name, string $code)
    {
        $this->id   = $id;
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * Constrói um DTO a partir de um Model Eloquent.
     */
    public static function fromModel(EstablishmentType $model): self
    {
        return new self(
            $model->id,
            $model->name,
            $model->code,
        );
    }

    /**
     * Retorna um array simples com os dados do DTO.
     */
    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'code' => $this->code,
        ];
    }
}
