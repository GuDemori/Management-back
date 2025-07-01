<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'establishment_type_id' => null,
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('Senha123!'),
            'role' => 'admin',
            'document' => $this->faker->numerify('###########'),
            'cep' => '87000000',
            'address' => $this->faker->streetName,
            'number' => $this->faker->buildingNumber,
            'complement' => '',
            'district' => $this->faker->citySuffix,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'refresh_token' => Str::uuid(),
            'refresh_token_expiry' => now()->addDays(30),
        ];
    }
}
