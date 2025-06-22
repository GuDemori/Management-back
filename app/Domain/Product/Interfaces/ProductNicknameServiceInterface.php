<?php

namespace App\Domain\Product\Interfaces;

use App\Domain\Product\DTOs\ProductNicknameDTO;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ProductNickname;

interface ProductNicknameServiceInterface
{
    /**
     * Recupera todos os apelidos de produtos.
     */
    public function getAll(): Collection;

    /**
     * Recupera os apelidos de um produto específico.
     */
    public function getByProduct(int $productId): Collection;

    /**
     * Recupera um apelido pelo ID.
     */
    public function getById(int $id): ProductNickname;

    /**
     * Cria um novo apelido.
     */
    public function create(ProductNicknameDTO $dto): ProductNickname;

    /**
     * Atualiza um apelido existente.
     */
    public function update(int $id, ProductNicknameDTO $dto): ProductNickname;

    /**
     * Exclui um apelido.
     */
    public function delete(int $id): void;
}
