<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'name'),
            ],
            'code' => [
                'required',
                'string',
                'size:3',
                Rule::unique('product_categories', 'code'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.unique'   => 'Já existe uma categoria de produto com este nome.',
            'code.required' => 'O campo código é obrigatório.',
            'code.size'     => 'O código deve ter exatamente 3 caracteres.',
            'code.unique'   => 'Já existe uma categoria de produto com este código.',
        ];
    }
}