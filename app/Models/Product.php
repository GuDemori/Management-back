<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'supplier_id',
        'product_category_id',
        'name',
        'description',
        'image_url',
        'costs',
        'wholesale_price',
        'retail_price',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function nicknames()
    {
        return $this->hasMany(ProductNickname::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

}