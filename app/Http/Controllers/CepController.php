<?php

namespace App\Http\Controllers;

use App\Services\CepLookupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CepController extends Controller
{
    public function __construct(private CepLookupService $service) {}

    public function show(string $cep)
    {
        $cleanCep = preg_replace('/\D/', '', $cep);

        try {
            Log::info('[CEP] Iniciando busca de endereço', ['cep' => $cleanCep]);

            $data = $this->service->buscarEnderecoPorCep($cleanCep);

            if (! $data) {
                Log::warning('[CEP] CEP não encontrado', ['cep' => $cleanCep]);

                return response()->json([
                    'message' => 'CEP não encontrado'
                ], 404);
            }

            Log::info('[CEP] Endereço retornado com sucesso', ['cep' => $cleanCep]);

            return response()->json($data);

        } catch (Throwable $e) {
            Log::error('[CEP] Erro ao buscar endereço', [
                'cep' => $cleanCep,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erro ao buscar endereço. Tente novamente mais tarde.'
            ], 500);
        }
    }
}