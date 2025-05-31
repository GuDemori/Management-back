<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\UserRole;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],

            'role' => ['sometimes', Rule::in(array_column(UserRole::cases(), 'value'))],

            'document' => ['required', 'string', 'unique:users,document', function ($attribute, $value, $fail) {
                $clean = preg_replace('/[^0-9]/', '', $value);

                if (strlen($clean) === 11 && !$this->isValidCpf($clean)) {
                    $fail('CPF inválido.');
                }

                if (strlen($clean) === 14 && !$this->isValidCnpj($clean)) {
                    $fail('CNPJ inválido.');
                }

                if (!in_array(strlen($clean), [11, 14])) {
                    $fail('O campo documento deve conter um CPF (11 dígitos) ou CNPJ (14 dígitos).');
                }
            }],

            'cep'        => ['nullable', 'string', 'size:8'],
            'address'    => ['nullable', 'string'],
            'number'     => ['nullable', 'string'],
            'complement' => ['nullable', 'string'],
            'district'   => ['nullable', 'string'],
            'city'       => ['nullable', 'string'],
            'state'      => ['nullable', 'string', 'size:2'],
        ];
    }

    private function isValidCpf(string $cpf): bool
    {
        if (preg_match('/(\d)\1{10}/', $cpf)) return false;

        for ($verifierPosition = 9; $verifierPosition < 11; $verifierPosition++) {
            $sum = 0;

            for ($index = 0; $index < $verifierPosition; $index++) {
                $weight = ($verifierPosition + 1) - $index;
                $sum += $cpf[$index] * $weight;
            }

            $expectedDigit = ((10 * $sum) % 11) % 10;

            if ((int) $cpf[$verifierPosition] !== $expectedDigit) {
                return false;
            }
        }

        return true;
    }

    private function isValidCnpj(string $cnpj): bool
    {
        if (preg_match('/(\d)\1{13}/', $cnpj)) return false;

        $tamanho = strlen($cnpj) - 2;
        $numeros = substr($cnpj, 0, $tamanho);
        $digitos = substr($cnpj, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;

        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }

        $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
        if ($resultado != $digitos[0]) return false;

        $tamanho += 1;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;

        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }

        $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
        return $resultado == $digitos[1];
    }
}
