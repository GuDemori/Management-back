<?php

namespace App\Http\Controllers;

use App\Domain\Order\DTOs\OrderCreateDTO;
use App\Domain\Order\DTOs\OrderItemDTO;
use App\Domain\Order\DTOs\OrderUpdateDTO;
use App\Enums\OrderStatus;
use App\Domain\Order\Interfaces\OrderServiceInterface;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        protected OrderServiceInterface $orderService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();
            $filters = [];

            if ($user->role === 'client') {
                $filters['client_id'] = $user->id;
            }

            Log::info('[Pedido] Iniciando listagem de pedidos', [
                'user_id' => $user->id,
                'role'    => $user->role,
                'filters' => $filters
            ]);

            $orders = $this->orderService->list($filters);

            Log::info('[Pedido] Listagem de pedidos concluída', [
                'user_id' => $user->id,
                'total'   => $orders->count()
            ]);

            return response()->json($orders);
        } catch (Throwable $e) {
            Log::error('[Pedido] Erro ao listar pedidos', [
                'user_id' => auth()->id(),
                'erro'    => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao buscar pedidos.'], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = auth()->user();

            Log::info('[Pedido] Iniciando busca por pedido', [
                'order_id' => $id,
                'user_id'  => $user->id
            ]);

            $order = $this->orderService->findById($id);

            if ($user->role === 'client' && $order->clientId !== $user->id) {
                Log::warning('[Pedido] Acesso negado ao pedido', [
                    'order_id' => $id,
                    'user_id'  => $user->id
                ]);

                return response()->json(['message' => 'Acesso não autorizado.'], 403);
            }

            Log::info('[Pedido] Pedido retornado com sucesso', [
                'order_id' => $id,
                'user_id'  => $user->id
            ]);

            return response()->json($order);
        } catch (Throwable $e) {
            Log::error('[Pedido] Erro ao buscar pedido', [
                'order_id' => $id,
                'user_id'  => auth()->id(),
                'erro'     => $e->getMessage(),
                'trace'    => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao buscar pedido.'], 500);
        }
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $authUser = auth()->user();
            $isClient = $authUser->role === 'client';

            $clientId = $isClient ? $authUser->id : $request->input('client_id');
            $client = User::findOrFail($clientId);

            Log::info('[Pedido] Iniciando criação de pedido', [
                'auth_user_id' => $authUser->id,
                'client_id'    => $client->id,
                'is_client'    => $isClient,
                'item_count'   => count($request->input('items'))
            ]);

            $dto = new OrderCreateDTO(
                clientId: $client->id,
                clientName: $client->name,
                addressStreet: $client->address,
                addressNumber: $client->number,
                addressDistrict: $client->district,
                addressCity: $client->city,
                addressState: $client->state,
                addressZipcode: $client->cep,
                items: collect($request->input('items'))->map(function ($item) {
                    $product = Product::findOrFail($item['product_id']);
                    $price = $product->retail_price;
                    return new OrderItemDTO(
                        productId: $product->id,
                        productName: $product->name,
                        quantity: $item['quantity'],
                        priceUnit: $price,
                        subtotal: $item['quantity'] * $price
                    );
                })->toArray()
            );

            $order = $this->orderService->create($dto);

            Log::info('[Pedido] Pedido criado com sucesso', [
                'order_id'   => $order->id,
                'client_id'  => $client->id
            ]);

            return response()->json($order, 201);
        } catch (Throwable $e) {
            Log::error('[Pedido] Erro ao criar pedido', [
                'auth_user_id' => auth()->id(),
                'erro'         => $e->getMessage(),
                'trace'        => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao criar pedido.'], 500);
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $status = $request->input('status');
            $newStatus = $status ? OrderStatus::from($status) : null;
            $items = $request->input('items');

            Log::info('[Pedido] Iniciando atualização de pedido', [
                'order_id'   => $id,
                'user_id'    => auth()->id(),
                'has_items'  => !empty($items),
                'new_status' => $status
            ]);

            $dto = new OrderUpdateDTO(
                orderId: $id,
                items: $items
                    ? collect($items)->map(fn($item) => new OrderItemDTO(
                        productId: $item['product_id'],
                        productName: $item['product_name'],
                        quantity: $item['quantity'],
                        priceUnit: $item['price_unit'],
                        subtotal: $item['quantity'] * $item['price_unit']
                    ))->toArray()
                    : null,
                newStatus: $newStatus,
                changedByUserId: auth()->id()
            );

            $order = $this->orderService->update($dto);

            Log::info('[Pedido] Pedido atualizado com sucesso', [
                'order_id' => $order->id,
                'user_id'  => auth()->id()
            ]);

            return response()->json($order);
        } catch (Throwable $e) {
            Log::error('[Pedido] Erro ao atualizar pedido', [
                'order_id' => $id,
                'user_id'  => auth()->id(),
                'erro'     => $e->getMessage(),
                'trace'    => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao atualizar pedido.'], 500);
        }
    }
}