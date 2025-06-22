<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id'         => 'nullable|exists:suppliers,id',
            'product_category_id' => 'required|exists:product_categories,id',
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'image_url'           => 'nullable|url|max:255',
            'costs'               => 'required|numeric|min:0',
            'wholesale_price'     => 'required|numeric|min:0',
            'retail_price'        => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.exists'           => 'O fornecedor informado não existe.',
            'product_category_id.required' => 'O campo categoria de produto é obrigatório.',
            'product_category_id.exists'   => 'A categoria de produto informada não existe.',
            'name.required'                => 'O nome do produto é obrigatório.',
            'name.string'                  => 'O nome do produto deve ser um texto.',
            'name.max'                     => 'O nome do produto não pode exceder 255 caracteres.',
            'description.string'           => 'A descrição deve ser um texto.',
            'image_url.url'                => 'A URL da imagem deve ser um endereço válido.',
            'image_url.max'                => 'A URL da imagem não pode exceder 255 caracteres.',
            'costs.required'               => 'O custo do produto é obrigatório.',
            'costs.numeric'                => 'O custo deve ser um valor numérico.',
            'costs.min'                    => 'O custo não pode ser negativo.',
            'wholesale_price.required'     => 'O preço de atacado é obrigatório.',
            'wholesale_price.numeric'      => 'O preço de atacado deve ser um valor numérico.',
            'wholesale_price.min'          => 'O preço de atacado não pode ser negativo.',
            'retail_price.required'        => 'O preço de varejo é obrigatório.',
            'retail_price.numeric'         => 'O preço de varejo deve ser um valor numérico.',
            'retail_price.min'             => 'O preço de varejo não pode ser negativo.',
        ];
    }
}
