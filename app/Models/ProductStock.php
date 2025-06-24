<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $table = 'product_stock';

    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'stock_id',
        'quantity',
        'min_stock',
        'isActive'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_stock' => 'integer',
        'isActive' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
