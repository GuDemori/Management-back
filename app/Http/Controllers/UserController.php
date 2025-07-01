<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Services\CepLookupService;
use Domain\User\DTOs\UserCreateDTO;
use Domain\User\DTOs\UserLoginDTO;
use Domain\User\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserServiceInterface $userService;
    protected CepLookupService $cepLookupService;

    public function __construct(
        UserServiceInterface $userService,
        CepLookupService $cepLookupService
    ) {
        $this->userService = $userService;
        $this->cepLookupService = $cepLookupService;
    }

    public function index(Request $request)
    {
        return response()->json($request->user());
    }

    public function getClients(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'coworker'])) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $clients = User::where('role', 'client')->get();

        return response()->json($clients);
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();

        $cepData = $this->cepLookupService->buscarEnderecoPorCep($data['cep']);

        $dto = new UserCreateDTO(
            establishment_type_id: $data['establishment_type_id'] ?? null,
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            role: 'client',
            document: $data['document'],
            cep: $data['cep'],
            address: $cepData['address'] ?? null,
            number: $data['number'] ?? null,
            complement: $cepData['complement'] ?? null,
            district: $cepData['district'] ?? null,
            city: $cepData['city'] ?? null,
            state: $cepData['state'] ?? null,
        );

        $this->userService->register($dto);

        return response()->json(['message' => 'Usuário cadastrado com sucesso.'], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $dto = new UserLoginDTO(
            email: $credentials['email'],
            password: $credentials['password']
        );

        try {
            $auth = $this->userService->login($dto);

            return response()->json([
                'access_token'  => $auth->accessToken,
                'refresh_token' => $auth->refreshToken,
                'expires_in'    => $auth->expiresIn,
                'token_type'    => 'Bearer'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }

    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        try {
            $auth = $this->userService->refreshToken($request->refresh_token);

            return response()->json([
                'access_token'  => $auth->accessToken,
                'refresh_token' => $auth->refreshToken,
                'expires_in'    => $auth->expiresIn,
                'token_type'    => 'Bearer'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Refresh token inválido ou expirado.'], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        $this->userService->logout($user->id);

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    public function show(int $id)
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        return response()->json($user);
    }

    public function clients(): JsonResponse
    {
        return response()->json($this->userService->listClients());
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $user->update($data);

        return response()->json(['message' => 'Usuário atualizado com sucesso.']);
    }

}
