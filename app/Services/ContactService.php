<?php

namespace App\Services;

use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContactService
{
    protected $repository;

    public function __construct(ContactRepository $repository)
    {
        $this->repository = $repository;
    }

    //função para buscar todos os contatos de um usuario, podendo ter filtros
    public function getAllContacts(int $userId, ?string $cpf = null, ?string $name = null)
    {
        return $this->repository->getAllByUserId($userId, $cpf, $name);
    }

    //fucção para criar um contato
    public function createContact(array $data, int $userId)
    {
        $cpf = $data['contact']['cpf'];

        // Verifica se o CPF já existe para o usuário
        $exists = $this->repository->existsByCpfAndUserId($cpf, $userId);
    
        if ($exists) {
            throw new \Exception("Já existe um contato com o CPF {$cpf} para este usuário.", 422);
        }


        $contactData = [
            'name' => $data['contact']['name'],
            'cpf' => $data['contact']['cpf'],
            'user_id' => $userId,  
        ];
    
        $phones = $data['phones'] ?? [];
    
        $addresses = $this->getAddressesWithCoordinates($data['addresses'] ?? []);

        return $this->repository->create([
            'contact' => $contactData,
            'phones' => $phones,
            'addresses' => $addresses,
        ]);
    }

    //função para editar um contato
    public function updateContact(array $data, int $userId)
    {
        $contactId = $data['contact']['id'];
        $contact = $this->repository->findById($contactId);
        
        if (!$contact) {
            throw new \Exception("Contato não encontrado", 404);
        }
    
        $contactData = [
            'name' => $data['contact']['name'] ?? $contact->name,
            'cpf' => $data['contact']['cpf'] ?? $contact->cpf,
        ];
    
        $phones = $data['phones'] ?? [];
        
        $addresses = isset($data['addresses']) 
            ? $this->getAddressesWithCoordinates($data['addresses'])
            : $contact->addresses->toArray();
    
        $formattedData = [
            'contact' => $contactData,
            'phones' => $phones,
            'addresses' => $addresses,
        ];
    
        return $this->repository->update($contact, $formattedData);
    }

    //função para deletar um contato
    public function deleteContact(int $contactId, int $userId)
    {
        $contact = $this->repository->findById($contactId);
        if (!$contact) {
            throw new \Exception('Contato não encontrado', 404);
        }

        $this->repository->delete($contact);
    }

    //função para manipular o array de address e inclui lat e lng
    private function getAddressesWithCoordinates(array $addresses): array
    {
        return array_map(function ($address) {
            $coordinates = getCoordinatesFromAddress($address['cep']);

            if ($coordinates) {
                $address['latitude'] = $coordinates['lat'];
                $address['longitude'] = $coordinates['lng'];
            } else {
                $address['latitude'] = null; // Define um fallback
                $address['longitude'] = null;
            }

            return $address;
        }, $addresses);
    }
}
