<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('product_category');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'name')->ignore($id),
            ],
            'code' => [
                'required',
                'string',
                'size:3',
                Rule::unique('product_categories', 'code')->ignore($id),
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
