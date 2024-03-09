<?php

namespace App\Adapters;

use App\Services\ViaCepService;

/**
 * Adaptador para o serviço de busca de CEP.
 *
 * Esta classe atua como um adaptador para o serviço de busca de CEP, implementando a interface CepServiceInterface.
 * Ela delega a chamada do método `searchCep` para a instância de serviço de busca de CEP fornecida durante a
 * inicialização. Isso permite a flexibilidade de trocar facilmente a implementação subjacente do serviço de busca
 * de CEP sem afetar o código que depende do adaptador.
 *
 * @category Classe
 * @package  App\Adapters
 */
class CepServiceAdapter implements CepServiceInterface
{
    private CepServiceInterface $cepService;

    public function __construct(CepServiceInterface $cepService)
    {
        $this->cepService = $cepService;
    }

    public function searchCep(string $cep): array
    {
        $cepData = $this->cepService->searchCep($cep);
        return $this->adaptCepData($cepData);
    }

    /**
     * Método para adaptar a estrutura de retorno de diferentes serviços
     * @param array $cepData
     * @return array
     */
    private function adaptCepData(array $cepData): array
    {
        // API Via Cep
        if ($this->cepService instanceof ViaCepService) {
            return $this->adaptViaCep($cepData);
        }

        // retorna os dados sem adaptação
        return $cepData;
    }

    private function adaptViaCep(array $cepData): array
    {
        return [
            'cep' => $cepData['cep'],
            'street' => $cepData['logradouro'],
            'complement' => $cepData['complemento'] ?? '',
            'neighborhood' => $cepData['bairro'],
            'city' => $cepData['localidade'],
            'state' => $cepData['uf'],
        ];
    }
}
