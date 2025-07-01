<?php

namespace Domain\User\Services;

use App\Models\User;
use Carbon\Carbon;
use Domain\User\DTOs\UserCreateDTO;
use Domain\User\DTOs\UserLoginDTO;
use Domain\User\DTOs\UserAuthResponseDTO;
use Domain\User\Interfaces\UserRepositoryInterface;
use Domain\User\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Enums\UserRole;
use Domain\User\DTOs\UserDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    public function register(UserCreateDTO $dto): void
    {
        try {
            Log::info('Iniciando registro de usuário', ['email' => $dto->email]);

            if (!in_array($dto->role, array_column(UserRole::cases(), 'value'))) {
                Log::warning('Tentativa de registro com role inválida', ['role' => $dto->role]);
                throw new \InvalidArgumentException('Role inválida.');
            }

            $dto = new UserCreateDTO(
                establishment_type_id: $dto->establishment_type_id,
                name: $dto->name,
                email: $dto->email,
                password: bcrypt($dto->password),
                role: 'client',
                document: $dto->document,
                cep: $dto->cep,
                address: $dto->address,
                number: $dto->number,
                complement: $dto->complement,
                district: $dto->district,
                city: $dto->city,
                state: $dto->state,
            );

            $this->repository->create($dto);

            Log::info('Usuário registrado com sucesso', ['email' => $dto->email]);
        } catch (Throwable $e) {
            Log::error('Erro ao registrar usuário', [
                'email' => $dto->email ?? null,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function login(UserLoginDTO $dto): UserAuthResponseDTO
    {
        try {
            Log::info('Tentativa de login', ['email' => $dto->email]);

            $user = $this->repository->findByEmail($dto->email);

            if (!$user || !Hash::check($dto->password, $user->password)) {
                Log::warning('Login falhou - credenciais inválidas', ['email' => $dto->email]);
                throw new \Exception('Credenciais inválidas');
            }

            $accessToken = JWTAuth::fromUser($user, [
                'exp' => Carbon::now()->addSeconds($this->accessTokenTTL($user))->timestamp
            ]);

            $refreshToken = Str::uuid()->toString();
            $refreshExpiresAt = Carbon::now()->addSeconds($this->refreshTokenTTL($user));

            $this->repository->saveRefreshToken($user->id, $refreshToken, $refreshExpiresAt);

            Log::info('Login bem-sucedido', ['user_id' => $user->id]);

            return new UserAuthResponseDTO(
                accessToken: $accessToken,
                refreshToken: $refreshToken,
                expiresIn: $this->accessTokenTTL($user)
            );
        } catch (Throwable $e) {
            Log::error('Erro no login', [
                'email' => $dto->email,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function refreshToken(string $refreshToken): UserAuthResponseDTO
    {
        try {
            Log::info('Tentativa de refresh token', ['refresh_token' => $this->maskToken($refreshToken)]);

            $user = $this->repository->getUserByRefreshToken($refreshToken);

            if (!$user || Carbon::now()->greaterThan($user->refresh_token_expiry)) {
                Log::warning('Refresh token inválido ou expirado', [
                    'refresh_token' => $this->maskToken($refreshToken),
                    'user_id' => $user?->id
                ]);
                throw new \Exception('Refresh token expirado ou inválido');
            }

            $accessToken = JWTAuth::fromUser($user, [
                'exp' => Carbon::now()->addSeconds($this->accessTokenTTL($user))->timestamp
            ]);

            Log::info('Access token gerado com sucesso via refresh', ['user_id' => $user->id]);

            return new UserAuthResponseDTO(
                accessToken: $accessToken,
                refreshToken: $refreshToken,
                expiresIn: $this->accessTokenTTL($user)
            );
        } catch (Throwable $e) {
            Log::error('Erro ao processar refresh token', [
                'refresh_token' => $this->maskToken($refreshToken),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    private function maskToken(string $token): string
    {
        return substr($token, 0, 4) . '****';
    }

    public function logout(int $userId): void
    {
        try {
            Log::info('Logout solicitado', ['user_id' => $userId]);

            $this->repository->revokeRefreshToken($userId);

            Log::info('Logout realizado com sucesso', ['user_id' => $userId]);
        } catch (Throwable $e) {
            Log::error('Erro ao realizar logout', [
                'user_id' => $userId,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function accessTokenTTL(User $user): int
    {
        return match ($user->role) {
            'admin', 'v1'     => 2 * 60 * 60,       // 2 horas
            'client', 'coworker'    => 5 * 24 * 60 * 60,  // 5 dias
            default     => 1 * 60 * 60,       // 1 hora
        };
    }

    private function refreshTokenTTL(User $user): int
    {
        return match ($user->role) {
            'admin', 'v1'     => 5 * 24 * 60 * 60,   // 5 dias
            'client', 'coworker'    => 30 * 24 * 60 * 60,  // 30 dias
            default     => 7 * 24 * 60 * 60,   // 7 dias
        };
    }

    public function findById(int $id): ?UserDTO
    {
        try {
            $user = $this->repository->findById($id);

            if (!$user) {
                Log::warning('Usuário não encontrado', ['user_id' => $id]);
                return null;
            }

            Log::info('Usuário encontrado com sucesso', ['user_id' => $id]);
            return UserDTO::fromModel($user);
        } catch (Throwable $e) {
            Log::error('Erro ao buscar usuário por ID', [
                'user_id' => $id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function listClients(): Collection
    {
        return $this->repository->getAllClients();
    }
}