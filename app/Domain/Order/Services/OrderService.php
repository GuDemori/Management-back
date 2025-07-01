<?php

namespace App\Domain\Order\Services;

use App\Domain\Order\DTOs\OrderCreateDTO;
use App\Domain\Order\DTOs\OrderItemDTO;
use App\Domain\Order\DTOs\OrderResponseDTO;
use App\Domain\Order\DTOs\OrderStatusHistoryDTO;
use App\Domain\Order\DTOs\OrderUpdateDTO;
use App\Enums\OrderStatus;
use App\Domain\Order\Interfaces\OrderItemHistoryRepositoryInterface;
use App\Domain\Order\Interfaces\OrderRepositoryInterface;
use App\Domain\Order\Interfaces\OrderServiceInterface;
use App\Domain\Order\Interfaces\OrderStatusHistoryRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemHistory;
use App\Models\OrderStatusHistory;
use Domain\ProductStock\Interfaces\ProductStockRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderItemHistoryRepositoryInterface $itemHistoryRepository,
        private readonly OrderStatusHistoryRepositoryInterface $statusHistoryRepository,
        private readonly ProductStockRepositoryInterface $productStockRepository
    ) {}

    public function create(OrderCreateDTO $dto): OrderResponseDTO
    {
        try {
            Log::info('[Pedido] Iniciando criação de pedido', ['client_id' => $dto->clientId]);

            return DB::transaction(function () use ($dto) {
                $order = new Order([
                    'client_id'               => $dto->clientId,
                    'client_name'             => $dto->clientName,
                    'client_address_street'   => $dto->addressStreet,
                    'client_address_number'   => $dto->addressNumber,
                    'client_address_district' => $dto->addressDistrict,
                    'client_address_city'     => $dto->addressCity,
                    'client_address_state'    => $dto->addressState,
                    'client_address_zipcode'  => $dto->addressZipcode,
                    'total_value'             => 0,
                    'status'                  => OrderStatus::EmEspera->value,
                ]);

                $this->orderRepository->persist($order);

                $total = 0;
                foreach ($dto->items as $itemDTO) {
                    $subtotal = $itemDTO->quantity * $itemDTO->priceUnit;
                    $total += $subtotal;

                    $order->items()->create([
                        'product_id'   => $itemDTO->productId,
                        'product_name' => $itemDTO->productName,
                        'quantity'     => $itemDTO->quantity,
                        'price_unit'   => $itemDTO->priceUnit,
                        'subtotal'     => $subtotal,
                    ]);
                }

                $order->update(['total_value' => $total]);

                $this->statusHistoryRepository->persist(new OrderStatusHistory([
                    'order_id' => $order->id,
                    'old_status' => OrderStatus::EmEspera->value,
                    'new_status' => OrderStatus::EmEspera->value,
                    'changed_at' => now(),
                    'changed_by_user_id' => auth()->id(),
                ]));

                Log::info('[Pedido] Pedido criado com sucesso', ['order_id' => $order->id]);

                return $this->mapToResponseDTO($order->fresh(['items', 'statusHistories']));
            });
        } catch (\Throwable $e) {
            Log::error('[Pedido] Erro ao criar pedido', [
                'client_id' => $dto->clientId,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function update(OrderUpdateDTO $dto): OrderResponseDTO
    {
        try {
            Log::info('[Pedido] Iniciando atualização', [
                'order_id' => $dto->orderId,
                'user_id' => $dto->changedByUserId
            ]);

            return DB::transaction(function () use ($dto) {
                $order = $this->orderRepository->find($dto->orderId);

                if (!$order) {
                    Log::warning('Pedido não encontrado na atualização', ['order_id' => $dto->orderId]);
                    throw new Exception("Pedido não encontrado");
                }

                $user = auth()->user();
                $isClient = $user->role === 'client';

                if ($isClient) {
                    if ($order->client_id !== $user->id) {
                        Log::warning('[Pedido] Cliente sem permissão', ['user_id' => $user->id, 'order_id' => $order->id]);
                        throw new Exception("Você não tem permissão para editar este pedido.");
                    }

                    if (!in_array($order->status, [OrderStatus::EmEspera->value])) {
                        Log::warning('Cliente tentou editar pedido com status inválido', ['order_id' => $order->id, 'status' => $order->status]);
                        throw new Exception("Clientes só podem editar pedidos com status 'Em espera'.");
                    }

                    if ($dto->newStatus && $dto->newStatus !== OrderStatus::Cancelado) {
                        Log::warning('Cliente tentou alterar status para não permitido', [
                            'order_id' => $order->id,
                            'new_status' => $dto->newStatus
                        ]);
                        throw new Exception("Clientes só podem cancelar seus próprios pedidos.");
                    }
                }

                if ($dto->items !== null && in_array($order->status, [OrderStatus::EmEspera->value, OrderStatus::Preparando->value])) {
                    foreach ($order->items as $oldItem) {
                        $matchingNew = collect($dto->items)->first(fn(OrderItemDTO $i) => $i->productId === $oldItem->product_id);

                        if ($matchingNew) {
                            $this->itemHistoryRepository->persist(new OrderItemHistory([
                                'order_item_id'       => $oldItem->id,
                                'old_product_id'      => $oldItem->product_id,
                                'old_product_name'    => $oldItem->product_name,
                                'old_quantity'        => $oldItem->quantity,
                                'old_price_unit'      => $oldItem->price_unit,
                                'old_subtotal'        => $oldItem->subtotal,
                                'new_product_id'      => $matchingNew->productId,
                                'new_product_name'    => $matchingNew->productName,
                                'new_quantity'        => $matchingNew->quantity,
                                'new_price_unit'      => $matchingNew->priceUnit,
                                'new_subtotal'        => $matchingNew->quantity * $matchingNew->priceUnit,
                                'changed_at'          => now(),
                                'changed_by_user_id'  => $dto->changedByUserId,
                            ]));
                        }
                    }

                    $order->items()->delete();

                    $newTotal = 0;
                    foreach ($dto->items as $itemDTO) {
                        $subtotal = $itemDTO->quantity * $itemDTO->priceUnit;
                        $newTotal += $subtotal;

                        $order->items()->create([
                            'product_id'   => $itemDTO->productId,
                            'product_name' => $itemDTO->productName,
                            'quantity'     => $itemDTO->quantity,
                            'price_unit'   => $itemDTO->priceUnit,
                            'subtotal'     => $subtotal,
                        ]);
                    }

                    $order->total_value = $newTotal;
                }

                // Alteração de status
                if ($dto->newStatus && $dto->newStatus->value !== $order->status) {
                    if (
                        $order->status === OrderStatus::Entregue->value &&
                        !in_array($dto->newStatus, [OrderStatus::Pago, OrderStatus::Cancelado])
                    ) {
                        Log::warning('Status inválido após entregue', [
                            'order_id' => $order->id,
                            'tentativa_de' => $order->status,
                            'tentativa_para' => $dto->newStatus
                        ]);
                        throw new Exception("Status 'Entregue' só pode ir para 'Pago' ou 'Cancelado'.");
                    }

                    $this->statusHistoryRepository->persist(new OrderStatusHistory([
                        'order_id'           => $order->id,
                        'old_status'         => $order->status,
                        'new_status'         => $dto->newStatus->value,
                        'changed_at'         => now(),
                        'changed_by_user_id' => $dto->changedByUserId,
                    ]));

                    $order->status = $dto->newStatus->value;
                }

                // Débito de estoque se ainda não foi feito
                if (
                    in_array($order->status, [OrderStatus::Entregue->value, OrderStatus::Pago->value]) &&
                    !$order->is_stock_debited
                ) {
                    foreach ($order->items as $item) {
                        $stockId = 1; // ou lógica para buscar o estoque correto
                        $productStock = $this->productStockRepository->find($item->product_id, $stockId);

                        $productStock->quantity -= $item->quantity;
                        $this->productStockRepository->update($productStock);
                    }

                    $order->is_stock_debited = true;
                }

                $this->orderRepository->update($order);

                Log::info('Pedido atualizado com sucesso', ['order_id' => $order->id]);

                return $this->mapToResponseDTO($order->fresh(['items', 'statusHistories']));
            });
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar pedido', [
                'order_id' => $dto->orderId,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }


    public function findById(int $id): OrderResponseDTO
    {
        try {
            Log::info('Busca de pedido iniciada', [
                'order_id' => $id,
                'user_id' => auth()->id()
            ]);

            $order = $this->orderRepository->find($id);

            if (!$order) {
                Log::warning('Pedido não encontrado', ['order_id' => $id]);
                throw new Exception("Pedido não encontrado");
            }

            $user = auth()->user();

            if ($user->role === 'client' && $order->client_id !== $user->id) {
                Log::warning('Acesso não autorizado ao pedido', [
                    'order_id' => $id,
                    'user_id' => $user->id
                ]);
                throw new Exception("Você não tem permissão para visualizar este pedido.");
            }

            Log::info('Pedido retornado com sucesso', ['order_id' => $id]);

            return $this->mapToResponseDTO($order);
        } catch (\Throwable $e) {
            Log::error('Erro ao buscar pedido por ID', [
                'order_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function list(array $filters = []): Collection
    {
        $orders = $this->orderRepository->findAll($filters);

        return $orders->map(fn(Order $order) => $this->mapToResponseDTO($order));
    }

    private function mapToResponseDTO(Order $order): OrderResponseDTO
    {
        $items = $this->mapItems($order);
        $history = $this->mapHistory($order);

        return new OrderResponseDTO(
            $order->id,
            $order->client_id,
            $order->client_name,
            $order->client_address_street,
            $order->client_address_number,
            $order->client_address_district,
            $order->client_address_city,
            $order->client_address_state,
            $order->client_address_zipcode,
            $items,
            (float) $order->total_value,
            OrderStatus::from($order->status),
            $history,
            $order->created_at->toDateTimeString(),
            $order->updated_at->toDateTimeString()
        );
    }

    private function mapItems(Order $order): array
    {
        return $order->items->map(fn(OrderItem $item) => new OrderItemDTO(
            $item->product_id,
            $item->product_name,
            $item->quantity,
            (float) $item->price_unit,
            (float) $item->subtotal
        ))->toArray();
    }

    private function mapHistory(Order $order): array
    {
        return $order->statusHistories->map(fn(OrderStatusHistory $h) => new OrderStatusHistoryDTO(
            OrderStatus::from($h->old_status),
            OrderStatus::from($h->new_status),
            $h->changed_at->toDateTimeString(),
            $h->changed_by_user_id
        ))->toArray();
    }
}