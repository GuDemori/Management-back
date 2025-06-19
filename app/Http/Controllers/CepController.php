<?php

namespace App\Http\Controllers;

use App\Services\CepLookupService;
use Illuminate\Http\Request;

class CepController extends Controller
{
    public function __construct(private CepLookupService $service) {}

    public function show(string $cep)
    {
        // opcional: limpar formatação
        $cleanCep = preg_replace('/\D/', '', $cep);

        $data = $this->service->buscarEnderecoPorCep($cleanCep);

        if (! $data) {
            return response()->json([
                'message' => 'CEP não encontrado'
            ], 404);
        }

        return response()->json($data);
    }
}
