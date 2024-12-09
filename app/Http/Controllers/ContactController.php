<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Services\ContactService;


class ContactController extends Controller
{
    protected $service;

    public function __construct(ContactService $service)
    {
        $this->service = $service;
    }

    public function getAllContactsByUser(Request $request)
    {
        $userId = $request->user()->id;

        $cpf = $request->query('cpf');
        $name = $request->query('name');

        $contacts = $this->service->getAllContacts($userId, $cpf, $name);

        return response()->json($contacts);

    }

    public function store(StoreContactRequest $request)
    {
        $userId = $request->user()->id;

        try {
            $contact = $this->service->createContact($request->all(), $userId);
            return response()->json($contact, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    public function update(UpdateContactRequest $request)
    {
        $userId = $request->user()->id;

        try {
            $contact = $this->service->updateContact($request->all(), $userId);
            return response()->json($contact);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    public function delete(Request $request, int $contactId)
    {
        $userId = $request->user()->id;

        try {
            $this->service->deleteContact($contactId, $userId);
            return response()->json(['message' => 'Contato deletado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
