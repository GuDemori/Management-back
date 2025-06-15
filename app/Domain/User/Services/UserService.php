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

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    public function register(UserCreateDTO $dto): void
    {
        if (!in_array($dto->role, array_column(UserRole::cases(), 'value'))) {
            throw new \InvalidArgumentException('Role inválida.');
        }

        $dto = new UserCreateDTO(
            name:       $dto->name,
            email:      $dto->email,
            password:   bcrypt($dto->password),
            role:       'client',
            document:   $dto->document,
            cep:        $dto->cep,
            address:    $dto->address,
            number:     $dto->number,
            complement: $dto->complement,
            district:   $dto->district,
            city:       $dto->city,
            state:      $dto->state,
        );

        $this->repository->create($dto);
    }

    public function login(UserLoginDTO $dto): UserAuthResponseDTO
    {
        $user = $this->repository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new \Exception('Credenciais inválidas');
        }

        $accessToken = JWTAuth::fromUser($user, [
            'exp' => Carbon::now()->addSeconds($this->accessTokenTTL($user))->timestamp
        ]);

        $refreshToken = Str::uuid()->toString();
        $refreshExpiresAt = Carbon::now()->addSeconds($this->refreshTokenTTL($user));

        $this->repository->saveRefreshToken($user->id, $refreshToken, $refreshExpiresAt);

        return new UserAuthResponseDTO(
            accessToken: $accessToken,
            refreshToken: $refreshToken,
            expiresIn: $this->accessTokenTTL($user)
        );
    }

    public function refreshToken(string $refreshToken): UserAuthResponseDTO
    {
        $user = $this->repository->getUserByRefreshToken($refreshToken);

        if (!$user || Carbon::now()->greaterThan($user->refresh_token_expiry)) {
            throw new \Exception('Refresh token expirado ou inválido');
        }

        $accessToken = JWTAuth::fromUser($user, [
            'exp' => Carbon::now()->addSeconds($this->accessTokenTTL($user))->timestamp
        ]);

        return new UserAuthResponseDTO(
            accessToken:    $accessToken,
            refreshToken:   $refreshToken,
            expiresIn:      $this->accessTokenTTL($user)
        );
    }

    public function logout(int $userId): void
    {
        $this->repository->revokeRefreshToken($userId);
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
}