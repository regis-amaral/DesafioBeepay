<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Models\Patient;
use Tests\TestCase;

class CepControllerTest extends TestCase
{
    /** @test */
    public function testeResultSerchValidCep()
    {
        $cep = '97547-000';

        $expected = [
            "cep" => "97547-000",
            "street" => "Avenida República Riograndense",
            "complement" => "",
            "neighborhood" => "Santos Dumont",
            "city" => "Alegrete",
            "state" => "RS"
        ];

        $response = $this->getJson('/api/search-cep/' . $cep);

        $response->assertStatus(200);

        $response->assertJson($expected);

    }

    /** @test */
    public function testeResultSearchInvalidCep()
    {
        $cep = '12345679';

        $expected = [
            "error" => "Cep não encontrado."
        ];

        $response = $this->getJson('/api/search-cep/' . $cep);

        $response->assertStatus(404);

        $response->assertJson($expected);

    }

    public function testeResultSearchInvalidRequisition()
    {
        $cep = '12345679a';

        $expected = [
            "error" => "Requisição inválida."
        ];

        $response = $this->getJson('/api/search-cep/' . $cep);

        $response->assertStatus(400);

        $response->assertJson($expected);

    }


}
