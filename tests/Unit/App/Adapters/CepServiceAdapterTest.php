<?php

namespace Tests\Unit\App\Adapters;

use App\Adapters\CepServiceAdapter;
use App\Adapters\CepServiceInterface;
use App\Services\ViaCepService;
use Tests\TestCase;

class CepServiceAdapterTest extends TestCase
{
    /** @test */
    public function testSearchCepUsingAdaptedService()
    {
        // Mock do serviço de busca de CEP
        $mockCepService = $this->getMockBuilder(ViaCepService::class)
            ->getMock();

        // Configuração do retorno do mock do serviço de busca de CEP
        $cepData = [
            'cep' => '97547-000',
            'logradouro' => 'Avenida República Riograndense',
            'complemento' => '',
            'bairro' => 'Santos Dumont',
            'localidade' => 'Alegrete',
            'uf' => 'RS',
            'ibge' => '4300406',
            'gia' => '',
            'ddd' => '55',
            'siafi' => '8507'
        ];
        $mockCepService->expects($this->once())
            ->method('searchCep')
            ->willReturn($cepData);

        // Instância do adaptador com o mock do serviço de busca de CEP
        $cepAdapter = new CepServiceAdapter($mockCepService);

        // Chamada do método de busca de CEP no adaptador
        $result = $cepAdapter->searchCep('97547-000');

        // Verificação do resultado esperado após a adaptação
        $expectedResult = [
            'cep' => '97547-000',
            'street' => 'Avenida República Riograndense',
            'complement' => '',
            'neighborhood' => 'Santos Dumont',
            'city' => 'Alegrete',
            'state' => 'RS',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testAdaptCepDataReturnsOriginalData()
    {
        // Mock do serviço de busca de CEP
        $mockCepService = $this->getMockBuilder(CepServiceInterface::class)
            ->getMock();

        // Configuração do retorno do mock do serviço de busca de CEP
        $cepData = [
            'cep' => '97547-000',
            'logradouro' => 'Avenida República Riograndense',
            'complemento' => '',
            'bairro' => 'Santos Dumont',
            'localidade' => 'Alegrete',
            'uf' => 'RS',
            'ibge' => '4300406',
            'gia' => '',
            'ddd' => '55',
            'siafi' => '8507'
        ];

        // Configuração do mock para retornar os dados de CEP não adaptados
        $mockCepService->expects($this->once())
            ->method('searchCep')
            ->willReturn($cepData);

        // Teste da adaptação dos dados de CEP
        $adapter = new CepServiceAdapter($mockCepService);
        $adaptedData = $adapter->searchCep('12345-678');

        // Verificação de que os dados de CEP não foram adaptados
        $this->assertEquals($cepData, $adaptedData);
    }
}
