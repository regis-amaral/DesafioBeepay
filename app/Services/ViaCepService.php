<?php

namespace App\Services;

use GuzzleHttp\Client;

use App\Adapters\CepServiceInterface;

class ViaCepService implements CepServiceInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }
    public function searchCep(string $cep): array
    {
        $response = $this->client->request('GET', "https://viacep.com.br/ws/{$cep}/json");

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        } else {
            throw new \Exception('Erro ao consultar o CEP');
        }
    }
}
