<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class ViaCepService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    //integracao com api ViaCep
    public function buscarEnderecoPorCep($cep)
    {
        try {
            $response = $this->client->get("https://viacep.com.br/ws/{$cep}/json/");
            
            $dados = json_decode($response->getBody()->getContents(), true);

            if (isset($dados['erro']) && $dados['erro']) {
                throw new Exception("CEP não encontrado.");
            }

            return $dados;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar endereço: " . $e->getMessage());
        }
    }
}
