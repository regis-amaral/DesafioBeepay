<?php

namespace App\Adapters;

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
