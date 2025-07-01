<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Níveis de log customizados para tipos específicos de exceção.
     */
    protected $levels = [];

    /**
     * Exceções que não devem ser reportadas.
     */
    protected $dontReport = [];

    /**
     * Campos que não devem ser exibidos em validações.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra callbacks para reportar exceções.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Você pode customizar aqui se quiser
        });
    }

    /**
     * Customiza a resposta HTTP de exceções.
     */
    public function render($request, Throwable $exception)
    {
        // Para requisições que esperam JSON (API)
        if ($request->expectsJson()) {
            Log::error('Erro não tratado', [
                'erro' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);

            return response()->json([
                'message' => App::isProduction()
                    ? 'Erro interno no servidor'
                    : $exception->getMessage(),

                'trace' => App::isProduction()
                    ? null
                    : $exception->getTrace(),
            ], 500);
        }

        // Para requisições normais (web)
        return parent::render($request, $exception);
    }
}