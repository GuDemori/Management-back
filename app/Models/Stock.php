<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'cep',
        'address',
        'number',
        'city',
        'state',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];
}
