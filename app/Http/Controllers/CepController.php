<?php

namespace App\Http\Controllers;

use App\Adapters\CepServiceAdapter;
use App\Services\ViaCepService;

class CepController extends Controller
{
    protected CepServiceAdapter $cepAdapter;

    public function __construct()
    {
        $service = new ViaCepService();
        $this->cepAdapter = new CepServiceAdapter($service);
    }

    public function searchCep($cep)
    {
        try{
            $address = $this->cepAdapter->searchCep($cep);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
        return response()->json($address);
    }
}
