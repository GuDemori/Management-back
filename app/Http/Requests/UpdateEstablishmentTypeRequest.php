<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstablishmentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('establishment_types', 'name')->ignore($id),
            ],
            'code' => [
                'required',
                'string',
                'size:3',
                Rule::unique('establishment_types', 'code')->ignore($id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.unique'   => 'Já existe um tipo de estabelecimento com este nome.',
            'code.required' => 'O campo código é obrigatório.',
            'code.size'     => 'O código deve ter exatamente 3 caracteres.',
            'code.unique'   => 'Já existe um tipo de estabelecimento com este código.',
        ];
    }
}
