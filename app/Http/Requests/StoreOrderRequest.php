<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = auth()->user();
        $isClient = $user->role === 'client';

        return [
            'client_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($isClient, $user) {
                    if ($isClient && $value != $user->id) {
                        $fail('Clientes só podem criar pedidos em seu próprio nome.');
                    }
                },
            ],            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_unit' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required'               => 'O nome do cliente é obrigatório.',
            'client_address_street.required'     => 'A rua do endereço é obrigatória.',
            'client_address_number.required'     => 'O número do endereço é obrigatório.',
            'client_address_district.required'   => 'O bairro é obrigatório.',
            'client_address_city.required'       => 'A cidade é obrigatória.',
            'client_address_state.required'      => 'O estado é obrigatório.',
            'client_address_zipcode.required'    => 'O CEP é obrigatório.',

            'items.required'                     => 'É necessário informar ao menos um item.',
            'items.array'                        => 'Os itens devem estar em formato de lista.',
            'items.*.product_id.required'        => 'O ID do produto é obrigatório.',
            'items.*.product_id.integer'         => 'O ID do produto deve ser um número inteiro.',
            'items.*.product_id.exists'          => 'O produto informado não existe.',
            'items.*.product_name.required'      => 'O nome do produto é obrigatório.',
            'items.*.product_name.string'        => 'O nome do produto deve ser um texto.',
            'items.*.product_name.max'           => 'O nome do produto não pode exceder 255 caracteres.',
            'items.*.quantity.required'          => 'A quantidade é obrigatória.',
            'items.*.quantity.integer'           => 'A quantidade deve ser um número inteiro.',
            'items.*.quantity.min'               => 'A quantidade deve ser maior que zero.',
            'items.*.price_unit.required'        => 'O preço unitário é obrigatório.',
            'items.*.price_unit.numeric'         => 'O preço unitário deve ser um valor numérico.',
            'items.*.price_unit.min'             => 'O preço unitário deve ser maior que zero.',
        ];
    }
}
