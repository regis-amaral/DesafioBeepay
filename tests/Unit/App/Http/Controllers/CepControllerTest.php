<?php

namespace Tests\Unit\App\Http\Controllers;

use App\Adapters\CepServiceAdapter;
use App\Http\Controllers\CepController;
use App\Services\ViaCepService;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Mockery;

class CepControllerTest extends TestCase
{

    /** @test */
    public function testAddressCanBeRetrieved()
    {
        $cep = '97547000';
        $addressData = [
            'cep' => '97547-000',
            'street' => 'Avenida República Riograndense',
            'complement' => '',
            'neighborhood' => 'Santos Dumont',
            'city' => 'Alegrete',
            'state' => 'RS',
        ];

        $controller = new CepController();
        $response = $controller->searchCep($cep);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($addressData, $response->getData(true));
    }

    /** @test */
    public function testErrorResponseIsReturnedWhenCepServiceThrowsException()
    {
        $cep = '12345679';
        $errorMessage = 'Cep não encontrado.';

        $controller = new CepController();
        $response = $controller->searchCep($cep);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(['error' => $errorMessage], $response->getData(true));
    }
}
