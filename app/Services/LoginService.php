<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use App\Repositories\UserRepository;
use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LoginService {

    protected $userRepository;

    public function __construct(UserRepository $userRepository, ContactRepository $repository)
    {
        $this->userRepository = $userRepository;
        $this->repository = $repository;

    }

    //função para cadastrar novo usuario
    public function register(array $data)
    {
        $user = $this->userRepository->create($data);
        
        return $this->generateTokenForUser($user);
    }

    //função para autenticar usuario
    public function authenticate(string $user, string $password): array
    {
        $user = $this->userRepository->findByUser($user);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new AuthenticationException('Credenciais inválidas.');
        }

        return $this->generateTokenForUser($user);
    }

    //função para revogar token do usuario e deslogar
    public function logout(string $token): void
    {
        $this->userRepository->revokeToken($token);
    }

    //função para gerar token para usuario no cadastro e no login
    private function generateTokenForUser($user): array
    {
        $token = $user->createToken('API Token')->plainTextToken;
    
        $expiration = Carbon::now()->addHours(8);
    
        $this->userRepository->saveTokenExpiration($token, $expiration);
    
        return [
            'user' => $user,
            'token' => $token,
            'expires_at' => $expiration->toDateTimeString()
        ];
    }

    //função para resetar a senha usando o email cadastrado
    public function resetPassword(string $email, string $password): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new Exception('Usuário não encontrado.');
        }

        $user->password = Hash::make($password);
        $user->save();
    }

    //função para deletar conta e seus dados associados
    public function deleteAccountAndContacts($request)
    {
        $user = $request->user();
        $password = $request->input('password');

        if (!Hash::check($password, $user->password)) {
            throw new Exception('Senha inválida.');
        }

        $this->repository->deleteAllContactsByUserId($user->id);

        $this->userRepository->deleteUserById($user->id);
    }


}