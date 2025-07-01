<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('Senha123!'),
                'role' => 'admin',
                'document' => '00000000000',
                'cep' => '87235000',
                'address' => 'Rua A',
                'number' => '100',
                'district' => 'Centro',
                'city' => 'Indianópolis',
                'state' => 'PR',
                'refresh_token' => Str::uuid(),
                'refresh_token_expiry' => Carbon::now()->addDays(7),
            ],
            [
                'name' => 'Coworker User',
                'email' => 'coworker@example.com',
                'password' => Hash::make('Senha123!'),
                'role' => 'coworker',
                'document' => '11111111111',
                'cep' => '87235000',
                'address' => 'Rua B',
                'number' => '200',
                'district' => 'Centro',
                'city' => 'Indianópolis',
                'state' => 'PR',
                'refresh_token' => Str::uuid(),
                'refresh_token_expiry' => Carbon::now()->addDays(7),
            ],
            [
                'name' => 'Cliente 1',
                'email' => 'cliente1@example.com',
                'password' => Hash::make('Senha123!'),
                'role' => 'client',
                'document' => '22222222222',
                'cep' => '87235000',
                'address' => 'Rua C',
                'number' => '300',
                'district' => 'Centro',
                'city' => 'Indianópolis',
                'state' => 'PR',
                'refresh_token' => Str::uuid(),
                'refresh_token_expiry' => Carbon::now()->addDays(7),
            ],
            [
                'name' => 'Cliente 2',
                'email' => 'cliente2@example.com',
                'password' => Hash::make('Senha123!'),
                'role' => 'client',
                'document' => '33333333333',
                'cep' => '87235000',
                'address' => 'Rua D',
                'number' => '400',
                'district' => 'Centro',
                'city' => 'Indianópolis',
                'state' => 'PR',
                'refresh_token' => Str::uuid(),
                'refresh_token_expiry' => Carbon::now()->addDays(7),
            ],
            [
                'name' => 'Cliente 3',
                'email' => 'cliente3@example.com',
                'password' => Hash::make('Senha123!'),
                'role' => 'client',
                'document' => '44444444444',
                'cep' => '87235000',
                'address' => 'Rua E',
                'number' => '500',
                'district' => 'Centro',
                'city' => 'Indianópolis',
                'state' => 'PR',
                'refresh_token' => Str::uuid(),
                'refresh_token_expiry' => Carbon::now()->addDays(7),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
