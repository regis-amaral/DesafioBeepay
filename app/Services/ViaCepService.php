<?php

namespace App\Services;

use GuzzleHttp\Client;

use App\Adapters\CepServiceInterface;
use Illuminate\Support\Facades\Cache;

class ViaCepService implements CepServiceInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }
    public function searchCep(string $cep): array
    {
        // Armazena o CEP em cache por 1 hora
        return Cache::remember('cep_' . $cep, 3600, function () use ($cep) {
            $client = new Client();
            $response = $client->request('GET', "https://viacep.com.br/ws/{$cep}/json");

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            } else {
                throw new \Exception('Erro ao consultar o CEP');
            }
        });
    }
}
