<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\OrderStatus;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:' . implode(',', OrderStatus::values()),
            'items'  => 'nullable|array|min:1',
            'items.*.product_id'   => 'required_with:items|integer|exists:products,id',
            'items.*.product_name' => 'required_with:items|string|max:255',
            'items.*.quantity'     => 'required_with:items|integer|min:1',
            'items.*.price_unit'   => 'required_with:items|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in'                          => 'O status informado não é válido.',
            'items.array'                        => 'Os itens devem estar em formato de lista.',
            'items.*.product_id.required_with'   => 'O ID do produto é obrigatório.',
            'items.*.product_id.integer'         => 'O ID do produto deve ser um número inteiro.',
            'items.*.product_id.exists'          => 'O produto informado não existe.',
            'items.*.product_name.required_with' => 'O nome do produto é obrigatório.',
            'items.*.product_name.string'        => 'O nome do produto deve ser um texto.',
            'items.*.product_name.max'           => 'O nome do produto não pode exceder 255 caracteres.',
            'items.*.quantity.required_with'     => 'A quantidade é obrigatória.',
            'items.*.quantity.integer'           => 'A quantidade deve ser um número inteiro.',
            'items.*.quantity.min'               => 'A quantidade deve ser maior que zero.',
            'items.*.price_unit.required_with'   => 'O preço unitário é obrigatório.',
            'items.*.price_unit.numeric'         => 'O preço unitário deve ser um valor numérico.',
            'items.*.price_unit.min'             => 'O preço unitário deve ser maior que zero.',
        ];
    }

}
