<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstablishmentType;

class EstablishmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Padaria',        'code' => 'PAD'],
            ['name' => 'Açougue',        'code' => 'ACO'],
            ['name' => 'Posto',          'code' => 'POS'],
            ['name' => 'Conveniência',   'code' => 'CON'],
            ['name' => 'Supermercado',   'code' => 'SUP'],
            ['name' => 'Hortifruti',     'code' => 'HOR'],
            ['name' => 'Restaurante',    'code' => 'RES'],
            ['name' => 'Lanchonete',     'code' => 'LAN'],
            ['name' => 'Distribuidora',  'code' => 'DIS'],
            ['name' => 'Pet Shop',       'code' => 'PET'],
        ];

        foreach ($categories as $category) {
            EstablishmentType::create($category);
        }
    }
}