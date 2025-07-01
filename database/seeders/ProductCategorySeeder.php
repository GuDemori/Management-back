<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bebida',    'code' => 'BEB'],
            ['name' => 'Doce',      'code' => 'DOC'],
            ['name' => 'Chocolate', 'code' => 'CHO'],
            ['name' => 'Chiclete',  'code' => 'CHI'],
            ['name' => 'Pirulito',  'code' => 'PIR'],
            ['name' => 'Salgadinho','code' => 'SAL'],
            ['name' => 'Bolacha',   'code' => 'BOL'],
            ['name' => 'Cereal',    'code' => 'CER'],
            ['name' => 'Café',      'code' => 'CAF'],
            ['name' => 'Água',      'code' => 'AGU'],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}
