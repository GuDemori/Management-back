<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CepLookupService
{
    public function buscarEnderecoPorCep(string $cep): ?array
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);

        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

        if ($response->failed() || isset($response['erro'])) {
            return null;
        }

        return [
            'address'     => $response['logradouro'],
            'district'    => $response['bairro'],
            'city'        => $response['localidade'],
            'state'       => $response['uf'],
            'complement'  => $response['complemento'],
        ];
    }
}
