<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $stocks = [
            [
                'cep'     => '87013-000',
                'address' => 'Av. Brasil',
                'number'  => '1000',
                'city'    => 'Maringá',
                'state'   => 'PR',
                'isActive'=> true,
            ],
            [
                'cep'     => '87045-000',
                'address' => 'Rua Paraná',
                'number'  => '250',
                'city'    => 'Maringá',
                'state'   => 'PR',
                'isActive'=> true,
            ],
            [
                'cep'     => '87200-000',
                'address' => 'Av. Heitor Furtado',
                'number'  => '500',
                'city'    => 'Paranavaí',
                'state'   => 'PR',
                'isActive'=> false,
            ],
        ];

        foreach ($stocks as $stock) {
            Stock::create($stock);
        }
    }
}