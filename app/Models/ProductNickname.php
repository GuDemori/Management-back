<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductNickname extends Model
{
    protected $fillable = [
        'product_id',
        'nickname',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
