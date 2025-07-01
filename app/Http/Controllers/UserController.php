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
use Illuminate\Support\Facades\Log;
use Throwable;

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
            Log::warning('Acesso negado à listagem de clientes', ['user_id' => $user->id, 'role' => $user->role]);
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        try {
            $clients = User::where('role', 'client')->get();

            Log::info('Listagem de clientes realizada', ['user_id' => $user->id, 'total' => $clients->count()]);
            return response()->json($clients);
        } catch (\Throwable $e) {
            Log::error('Erro ao listar clientes', [
                'user_id' => $user->id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao listar clientes.'], 500);
        }
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();

        try {
            Log::info('Tentativa de registro de usuário', ['email' => $data['email']]);

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

            Log::info('Usuário registrado com sucesso', ['email' => $data['email']]);

            return response()->json(['message' => 'Usuário cadastrado com sucesso.'], 201);
        } catch (\Throwable $e) {
            Log::error('Erro no registro de usuário', [
                'email' => $data['email'],
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Erro ao cadastrar usuário.'], 500);
        }
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
            Log::info('Tentativa de login', ['email' => $dto->email]);

            $auth = $this->userService->login($dto);

            Log::info('Login realizado com sucesso', ['user_id' => auth()->id()]);

            return response()->json([
                'access_token'  => $auth->accessToken,
                'refresh_token' => $auth->refreshToken,
                'expires_in'    => $auth->expiresIn,
                'token_type'    => 'Bearer'
            ]);
        } catch (\Throwable $e) {
            Log::warning('Falha no login', [
                'email' => $dto->email,
                'erro'  => $e->getMessage()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Credenciais inválidas ou erro no login.'
            ], 401);
        }
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $maskedToken = substr($request->refresh_token, 0, 4) . '****';

        try {
            Log::info('Tentativa de refresh token', ['refresh_token' => $maskedToken]);

            $auth = $this->userService->refreshToken($request->refresh_token);

            Log::info('Refresh token bem-sucedido');

            return response()->json([
                'access_token'  => $auth->accessToken,
                'refresh_token' => $auth->refreshToken,
                'expires_in'    => $auth->expiresIn,
                'token_type'    => 'Bearer'
            ]);
        } catch (\Throwable $e) {
            Log::warning('Erro ao processar refresh token', [
                'refresh_token' => $maskedToken,
                'erro' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Refresh token inválido ou expirado.'], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            Log::warning('Tentativa de logout sem autenticação');
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        try {
            $this->userService->logout($user->id);

            Log::info('Logout realizado com sucesso', ['user_id' => $user->id]);

            return response()->json(['message' => 'Logout realizado com sucesso.']);
        } catch (\Throwable $e) {
            Log::error('Erro ao realizar logout', [
                'user_id' => $user->id,
                'erro' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Erro ao realizar logout.'], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $user = $this->userService->findById($id);

            if (!$user) {
                Log::warning('Usuário não encontrado', ['user_id' => $id]);
                return response()->json(['message' => 'Usuário não encontrado.'], 404);
            }

            Log::info('Usuário retornado com sucesso', ['user_id' => $id]);

            return response()->json($user);
        } catch (\Throwable $e) {
            Log::error('Erro ao buscar usuário por ID', [
                'user_id' => $id,
                'erro' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Erro ao buscar usuário.'], 500);
        }
    }

    public function clients(): JsonResponse
    {
        return response()->json($this->userService->listClients());
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();

        try {
            $user = User::find($id);

            if (!$user) {
                Log::warning('Tentativa de atualizar usuário inexistente', ['user_id' => $id]);
                return response()->json(['message' => 'Usuário não encontrado.'], 404);
            }

            $user->update($data);

            Log::info('Usuário atualizado com sucesso', ['user_id' => $id]);

            return response()->json(['message' => 'Usuário atualizado com sucesso.']);
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar usuário', [
                'user_id' => $id,
                'erro' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Erro ao atualizar usuário.'], 500);
        }
    }

    public function listAll(): JsonResponse
    {
        try {
            $users = User::with('establishmentType')
                ->whereIn('role', ['admin', 'coworker'])
                ->get();

            return response()->json($users);
        } catch (Throwable $e) {
            Log::error('Erro ao listar usuários (admin/coworker)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Erro ao buscar usuários.',
            ], 500);
        }
    }


    public function destroy(User $user): JsonResponse
    {
        try {
            $user->is_active = false;
            $user->save();

            Log::info("Usuário desativado com sucesso", [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            return response()->json([
                'message' => 'Usuário desativado com sucesso.'
            ]);
        } catch (Throwable $e) {
            Log::error("Erro ao desativar usuário", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id ?? null,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Erro ao desativar usuário.'
            ], 500);
        }
    }

    public function deactivateClient(int $id): JsonResponse
    {
        $client = User::where('role', 'client')->findOrFail($id);

        $client->is_active = false;
        $client->save();

        return response()->json(['message' => 'Cliente desativado com sucesso.']);
    }
}
