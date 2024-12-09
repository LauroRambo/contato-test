<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LoginService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use App\Exceptions\AuthenticationException;


class LoginController extends Controller
{
    public function __construct(LoginService $loginService){
        $this->loginService = $loginService;
    }

    public function register(RegisterRequest $request) {
        try {
            return response()->json(
                $this->loginService->register($request->validated()),
                201
             );
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage() . ' at ' . $e->getFile() . ' on line: ' . $e->getLine(),
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('user', 'password');
            $result = $this->loginService->authenticate($credentials['user'], $credentials['password']);

            return response()->json([
                'message' => 'Login bem-sucedido',
                'user' => $result['user'],
                'token' => $result['token'],
                'expires_at' => $result['expires_at']
            ]);
        } catch (AuthenticationException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token não fornecido.'], 400);
        }

        $this->loginService->logout($token);

        return response()->json(['message' => 'Logout bem-sucedido']);
    }

    public function resetPassword(PasswordResetRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $this->loginService->resetPassword($validated['email'], $validated['password']);

            return response()->json([
                'message' => 'Senha atualizada com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao atualizar a senha: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteAccount(Request $request)
    {

        try {
            $this->loginService->deleteAccountAndContacts($request);
            return response()->json(['message' => 'Conta e contatos deletados com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

}
