<?php

namespace App\Repositories;

use App\Models\Contact;

class ContactRepository
{
    public function getAllByUserId(int $userId, ?string $cpf = null, ?string $name = null, ?string $orderBy = 'name', 
    ?string $direction = 'asc')
    {
        
        $query = Contact::where('user_id', $userId)->with(['phones', 'addresses']);

        if ($cpf) {
            $query->where('cpf', 'like', "%{$cpf}%");
        }

        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }

        $query->orderBy($orderBy, $direction);

        return $query->paginate(10);
    }

    public function findById(int $contactId)
    {
        return Contact::where('id', $contactId)->with(['phones', 'addresses'])->first();
    }

    public function getContactsByFilters(int $userId)
    {
        return Contact::where('user_id', $userId)->with(['phones', 'addresses'])->get();
    }

    public function create(array $data)
    {        
        $contact = Contact::create($data['contact']);
    
        if (isset($data['phones']) && !empty($data['phones'])) {
            $contact->phones()->createMany($data['phones']);
        }


        if (isset($data['addresses']) && !empty($data['addresses'])) {
            $contact->addresses()->createMany($data['addresses']);
        }

        return $contact;
    }

    public function update(Contact $contact, array $data)
    {
        $contact->update($data['contact']);
        if (isset($data['phones'])) {
            $contact->phones()->delete(); 
            $contact->phones()->createMany($data['phones']); 
        }
        if (isset($data['addresses'])) {
            $contact->addresses()->delete(); 
            $contact->addresses()->createMany($data['addresses']); 
        }
        return $contact;
    }

    public function delete(Contact $contact)
    {
        $contact->delete();
    }

    public function deleteAllContactsByUserId(int $userId)
    {
        Contact::where('user_id', $userId)->delete();
    }

    public function existsByCpfAndUserId(string $cpf, int $userId): bool
    {
        return Contact::where('cpf', $cpf)->where('user_id', $userId)->exists();
    }
}
