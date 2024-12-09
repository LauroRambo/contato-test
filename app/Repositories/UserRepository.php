<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\PersonalAccessTokens;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByUser(string $user): ?User
    {
        return User::where('user', $user)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    //nas funções abaixo utilizo a classe PersonalAccessToken do sanctum para atrelar token com o usuario

    public function saveTokenExpiration(string $token, Carbon $expiration): void
    {
        PersonalAccessToken::findToken($token)
        ->update(['expires_at' => $expiration]);
    }

    public function isTokenExpired(string $token): bool
    {
        $expiration = PersonalAccessToken::findToken($token)
            ->value('expires_at');

        return Carbon::parse($expiration)->isPast();
    }

    
    public function revokeToken(string $token): void
    {
        PersonalAccessToken::findToken($token)->delete();
    }

    public function deleteUserById(int $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado', 404);
        }
        
        $user->delete();
    }
}
