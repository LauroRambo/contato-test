<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->user()->id; 
        $contactId = $this->input('contact.id');

        return [
            'contact.id' => 'required|numeric|exists:contacts,id,user_id,' . $userId, 
            'contact.name' => 'sometimes|required|string|max:255',
            'contact.cpf' => "sometimes|required|string|max:14",
            'phones' => 'sometimes|array',
            'phones.*.phone' => 'required|string|max:15',  
            'addresses' => 'sometimes|array',
            'addresses.*.address' => 'required|string|max:255',
            'addresses.*.number' => 'required|string|max:10',
            'addresses.*.cep' => 'required|string|max:9'
        ];
    }
}
