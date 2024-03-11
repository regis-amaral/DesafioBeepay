<?php

namespace App\Services;

use GuzzleHttp\Client;

use App\Adapters\CepServiceInterface;
use Illuminate\Support\Facades\Cache;

/**
 * Implementação do serviço de busca de CEP utilizando a API do ViaCEP.
 *
 * Esta classe implementa a interface CepServiceInterface e fornece um método para buscar informações de um CEP
 * específico utilizando a API do ViaCEP. Os resultados da busca são armazenados em cache por 1 hora para melhor
 * desempenho e eficiência. Se ocorrer um erro durante a consulta, uma exceção será lançada para lidar com o erro
 * de forma adequada.
 *
 * @category Classe
 * @package  App\Services
 */
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
//            return Cache::remember('cep_' . $cep, 3600, function () use ($cep) {
                try {
                    $client = new Client();
                        $response = $client->request('GET', "https://viacep.com.br/ws/{$cep}/json");
                }catch (\Exception $e){
                    if ($e->hasResponse() && $e->getResponse()->getStatusCode() == 400) {
                        throw new \Exception('Requisição inválida.',$e->getResponse()->getStatusCode());
                    } else {
                        $statusCode = 500;
                    }
                    throw new \Exception('Erro ao consultar o CEP.', $statusCode);
                }
                $responseData = json_decode($response->getBody()->getContents(), true);
                if ($response->getStatusCode() == 200) {
                    if (isset($responseData['erro']) && $responseData['erro'] == true) {
                        throw new \Exception('Cep não encontrado.', 404);
                    }
                    return $responseData;
                }
                throw new \Exception('Erro ao consultar o CEP.', 500);
//            });
    }
}
