<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PasswordResetRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.exists' => 'O e-mail fornecido não está registrado.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.confirmed' => 'As senhas não coincidem.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'sucesso' => false,
            'mensagem' => 'Campos inválidos',
            'erros' => $validator->errors()
        ]));
    }
}
