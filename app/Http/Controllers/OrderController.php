<?php

namespace App\Http\Controllers;

use App\Domain\Order\DTOs\OrderCreateDTO;
use App\Domain\Order\DTOs\OrderItemDTO;
use App\Domain\Order\DTOs\OrderUpdateDTO;
use App\Enums\OrderStatus;
use App\Domain\Order\Interfaces\OrderServiceInterface;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderServiceInterface $orderService
    ) {}

    public function index(): JsonResponse
    {
        $user = auth()->user();
        $filters = [];

        if ($user->role === 'client') {
            $filters['client_id'] = $user->id;
        }

        $orders = $this->orderService->list($filters);

        return response()->json($orders);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->findById($id);

        if (auth()->user()->role === 'client' && $order->clientId !== auth()->id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        return response()->json($order);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $authUser = auth()->user();
        $isClient = $authUser->role === 'client';

        $clientId = $isClient ? $authUser->id : $request->input('client_id');
        $client = \App\Models\User::findOrFail($clientId);

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
                $product = \App\Models\Product::findOrFail($item['product_id']);
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

        return response()->json($order, 201);
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        $status = $request->input('status');
        $newStatus = $status ? OrderStatus::from($status) : null;
        $items = $request->input('items');

        $dto = new OrderUpdateDTO(
            orderId: $id,
            items: $items
                ? collect($items)->map(fn ($item) => new OrderItemDTO(
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

        return response()->json($order);
    }

}