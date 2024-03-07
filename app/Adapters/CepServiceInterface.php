<?php

namespace App\Adapters;

interface CepServiceInterface
{
    public function searchCep(string $cep): array;
}
