<?php

namespace App\Http\Controllers;

use App\Services\CepLookupService;
use Illuminate\Http\Request;
use Domain\Stock\DTOs\StockDTO;
use Domain\Stock\Interfaces\StockServiceInterface;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function __construct(
        private StockServiceInterface $stockService,
        private CepLookupService $cepService
    ) {}

    public function index()
    {
        return response()->json($this->stockService->all());
    }

    public function show($id)
    {
        return response()->json($this->stockService->find($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cep'    => 'required|string|size:9',
            'number' => 'required|string|max:10',
        ]);

        $cepData = $this->cepService->buscarEnderecoPorCep($data['cep']);
        if (! $cepData) {
            return response()->json(['cep' => 'CEP não encontrado'], 422);
        }

        $logradouro = $cepData['address'] ?? '';

        $data['address'] = $request->input('address', $logradouro);
        $data['city']    = $cepData['city'];
        $data['state']   = $cepData['state'];
        Validator::make($data, [
            'address' => 'required|string|max:255',
            'city'    => 'required|string|max:100',
            'state'   => 'required|string|max:100',
        ])->validate();

        $dto   = StockDTO::fromArray($data);
        $stock = $this->stockService->create($dto);

        return response()->json($stock, 201);
    }

    public function update(Request $request, $id)
    {
        $dto = StockDTO::fromArray($this->validateData($request));
        return response()->json($this->stockService->update($id, $dto));
    }

    public function destroy($id)
    {
        $this->stockService->delete($id);
        return response()->noContent();
    }

    private function validateData(Request $request): array
    {

        $data = $request->validate([
            'cep'      => 'required|string|size:9',
            'address'  => 'nullable|string|max:255',
            'number'   => 'nullable|string|max:10',
            'city'     => 'nullable|string|max:100',
            'state'    => 'nullable|string|max:100',
            'isActive' => 'boolean',
        ]);

        return $data;
    }
}