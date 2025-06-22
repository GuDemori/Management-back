<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:255',
            'document'     => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'O nome do fornecedor é obrigatório.',
            'name.string'       => 'O nome do fornecedor deve ser um texto.',
            'name.max'          => 'O nome do fornecedor não pode exceder 255 caracteres.',
            'company_name.string' => 'O nome da empresa deve ser um texto.',
            'company_name.max'    => 'O nome da empresa não pode exceder 255 caracteres.',
            'email.email'         => 'O e-mail deve ser um endereço válido.',
            'email.max'           => 'O e-mail não pode exceder 255 caracteres.',
            'phone.string'        => 'O telefone deve ser um texto.',
            'phone.max'           => 'O telefone não pode exceder 255 caracteres.',
            'document.string'     => 'O documento deve ser um texto.',
            'document.max'        => 'O documento não pode exceder 255 caracteres.',
            'city.string'         => 'A cidade deve ser um texto.',
            'city.max'            => 'A cidade não pode exceder 255 caracteres.',
        ];
    }
}