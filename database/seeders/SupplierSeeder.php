<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Fornecedor A',
                'company_name' => 'Fornecedor A Ltda',
                'email' => 'fornecedorA@example.com',
                'phone' => '(44) 99999-0001',
                'document' => '12345678000100',
                'city' => 'Maringá',
            ],
            [
                'name' => 'Fornecedor B',
                'company_name' => 'Fornecedor B Ltda',
                'email' => 'fornecedorB@example.com',
                'phone' => '(44) 99999-0002',
                'document' => '22345678000100',
                'city' => 'Paranavaí',
            ],
            [
                'name' => 'Fornecedor C',
                'company_name' => 'Fornecedor C Ltda',
                'email' => 'fornecedorC@example.com',
                'phone' => '(44) 99999-0003',
                'document' => '32345678000100',
                'city' => 'Cianorte',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}