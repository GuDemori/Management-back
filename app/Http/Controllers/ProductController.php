<?php

namespace App\Http\Controllers;

use App\Domain\Product\DTOs\ProductDTO;
use App\Domain\Product\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $stockId = $request->query('stock_id');

            Log::info('[Produto] Iniciando listagem de produtos', [
                'user_id' => auth()->id(),
                'stock_id' => $stockId,
            ]);

            $products = $this->productService->getAll($stockId);

            Log::info('[Produto] Listagem concluída', [
                'total' => count($products),
            ]);

            return response()->json($products);
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao listar produtos', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return response()->json(['message' => 'Erro ao listar produtos.'], 500);
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            Log::info('[Produto] Iniciando busca por produto', [
                'product_id' => $id,
                'user_id' => auth()->id(),
            ]);

            $product = $this->productService->getById($id);

            if (!$product) {
                Log::warning('[Produto] Produto não encontrado', ['product_id' => $id]);
                return response()->json(['message' => 'Produto não encontrado.'], 404);
            }

            Log::info('[Produto] Produto retornado com sucesso', ['product_id' => $id]);

            return response()->json($product);
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao buscar produto', [
                'product_id' => $id,
                'user_id' => auth()->id(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Erro ao buscar produto.'], 500);
        }
    }


    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            Log::info('[Produto] Iniciando criação de produto', ['user_id' => $userId]);

            $dto = ProductDTO::fromArray($request->validated());
            $product = $this->productService->create($dto);

            $nicknames = $request->input('nicknames');
            if (is_array($nicknames)) {
                foreach ($nicknames as $nickname) {
                    $product->nicknames()->create(['nickname' => $nickname]);
                }
            }

            Log::info('[Produto] Produto criado com sucesso', [
                'product_id' => $product->id,
                'user_id' => $userId
            ]);

            return response()->json($product, Response::HTTP_CREATED);
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao criar produto', [
                'user_id' => auth()->id(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Erro ao criar produto.'], 500);
        }
    }


    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $dto = ProductDTO::fromArray($request->validated());
        $product = $this->productService->update($id, $dto);
        if (isset($data['nicknames'])) {
            foreach ($data['nicknames'] as $nickname) {
                $product->nicknames()->create(['nickname' => $nickname]);
            }
        }
        return response()->json($product);
    }

    public function updateNicknames(Request $request, Product $product): JsonResponse
    {
        try {
            $userId = auth()->id();

            Log::info('[Produto] Iniciando atualização de apelidos', [
                'product_id' => $product->id,
                'user_id' => $userId
            ]);

            $request->validate([
                'nicknames'   => 'required|array',
                'nicknames.*' => 'required|string|max:255',
            ]);

            $product->nicknames()->delete();

            foreach ($request->nicknames as $nickname) {
                $product->nicknames()->create(['nickname' => $nickname]);
            }

            Log::info('[Produto] Apelidos atualizados com sucesso', [
                'product_id' => $product->id,
                'user_id' => $userId
            ]);

            return response()->json(['message' => 'Apelidos atualizados.']);
        } catch (\Throwable $e) {
            Log::error('[Produto] Erro ao atualizar apelidos', [
                'product_id' => $product->id ?? null,
                'user_id' => auth()->id(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Erro ao atualizar apelidos.'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Log::info('[Produto] Iniciando exclusão de produto', [
                'product_id' => $id,
                'user_id' => auth()->id()
            ]);

            $this->productService->delete($id);

            Log::info('[Produto] Produto excluído com sucesso', [
                'product_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao excluir produto', [
                'product_id' => $id,
                'user_id' => auth()->id(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao excluir produto.'], 500);
        }
    }

    public function searchByNickname(Request $request): JsonResponse
    {
        $term = $request->query('nickname', '');

        try {
            Log::info('[Produto] Iniciando busca por apelido', [
                'termo' => $term,
                'user_id' => auth()->id()
            ]);

            $products = $this->productService->searchByNickname($term);

            Log::info('[Produto] Busca por apelido concluída', [
                'termo' => $term,
                'quantidade' => count($products)
            ]);

            return response()->json($products);
        } catch (Throwable $e) {
            Log::error('[Produto] Erro na busca por apelido', [
                'termo' => $term,
                'user_id' => auth()->id(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao buscar produtos por apelido.'], 500);
        }
    }

    public function removeOrphanImages(): void
    {
        try {
            Log::info('[Produto] Iniciando verificação de imagens órfãs', [
                'user_id' => auth()->id()
            ]);

            $allProductImages = Product::pluck('image_url')->map(function ($url) {
                return ltrim(parse_url($url, PHP_URL_PATH), '/');
            })->toArray();

            $allImagesInS3 = Storage::disk('s3')->files('products');

            $orphans = array_diff($allImagesInS3, $allProductImages);

            foreach ($orphans as $imageUrl) {
                Storage::disk('s3')->delete($imageUrl);
                Log::info('[Produto] Imagem órfã removida', ['image' => $imageUrl]);
            }

            Log::info('[Produto] Verificação de imagens órfãs concluída', [
                'removidas' => count($orphans)
            ]);
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao remover imagens órfãs', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
        }
    }

    public function deleteImageFromS3(string $imageUrl): void
    {
        try {
            Log::info('[Produto] Tentando excluir imagem do S3', ['image' => $imageUrl]);

            if (Storage::disk('s3')->exists($imageUrl)) {
                Storage::disk('s3')->delete($imageUrl);
                Log::info('[Produto] Imagem excluída do S3 com sucesso', ['image' => $imageUrl]);
            } else {
                Log::warning('[Produto] Imagem não encontrada no S3 para exclusão', ['image' => $imageUrl]);
            }
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao excluir imagem do S3', [
                'image' => $imageUrl,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function deactivate(Product $product): JsonResponse
    {
        try {
            Log::info('[Produto] Iniciando desativação do produto', ['product_id' => $product->id]);

            if ($product->image_url) {
                $imagePath = ltrim(parse_url($product->image_url, PHP_URL_PATH), '/');

                Log::info('[Produto] Tentando excluir imagem do S3', ['image' => $imagePath]);
                $this->deleteImageFromS3($imagePath);

                $product->image_url = null;
            }

            $product->is_active = false;
            $product->save();

            Log::info('[Produto] Produto desativado com sucesso', ['product_id' => $product->id]);

            $this->removeOrphanImages();

            return response()->json(['message' => 'Produto desativado com sucesso.']);
        } catch (Throwable $e) {
            Log::error('[Produto] Erro ao desativar produto', [
                'product_id' => $product->id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao desativar produto.'], 500);
        }
    }
}
