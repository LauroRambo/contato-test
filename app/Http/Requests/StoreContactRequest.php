<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'contact.name' => 'required|string|max:255',
            'contact.cpf' => 'required|string|max:14',
            
            'phones' => 'required|array',
            'phones.*.phone' => 'required|string|max:15',

            'addresses' => 'required|array',
            'addresses.*.address' => 'required|string|max:255',
            'addresses.*.number' => 'required|numeric',
            'addresses.*.cep' => 'required|string|max:9'
        ];
    }

    public function messages(): array
    {
        return [
            'contact.name.required' => 'O nome é obrigatório.',
            'contact.cpf.required' => 'O CPF é obrigatório.',
            'contact.cpf.unique' => 'Este CPF já está cadastrado para o seu usuário.',
            
            'phones.required' => 'É necessário fornecer ao menos um telefone.',
            'phones.*.phone.required' => 'O telefone é obrigatório.',
            
            'addresses.required' => 'É necessário fornecer ao menos um endereço.',
            'addresses.*.address.required' => 'O endereço é obrigatório.',
            'addresses.*.number.required' => 'O número do endereço é obrigatório.',
            'addresses.*.cep.required' => 'O CEP é obrigatório.',
            'addresses.*.latitude.required' => 'A latitude é obrigatória.',
            'addresses.*.longitude.required' => 'A longitude é obrigatória.',
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
