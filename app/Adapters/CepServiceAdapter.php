<?php

namespace App\Adapters;

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
        return $this->cepService->searchCep($cep);
    }
}
