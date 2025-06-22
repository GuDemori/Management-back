<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemHistory extends Model
{
    protected $table = 'order_items_histories';

    public $timestamps = false;

    protected $fillable = [
        'order_item_id',
        'old_product_id',
        'old_product_name',
        'old_quantity',
        'old_price_unit',
        'old_subtotal',
        'new_product_id',
        'new_product_name',
        'new_quantity',
        'new_price_unit',
        'new_subtotal',
        'changed_at',
        'changed_by_user_id',
    ];

    protected $casts = [
        'old_price_unit' => 'decimal:2',
        'old_subtotal' => 'decimal:2',
        'new_price_unit' => 'decimal:2',
        'new_subtotal' => 'decimal:2',
        'changed_at' => 'datetime',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}