<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function welcomeReturnsApiStatus()
    {
        // Faz a solicitação GET à rota /welcome
        $response = $this->get('/');

        // Verifica se a resposta possui o status HTTP 200 (OK)
        $response->assertStatus(200);

        // Verifica se a resposta contém o conteúdo esperado
        $response->assertJson([
            'status' => 'API Online'
        ]);
    }

    /** @test */
    public function testIndexReturnsPatientCollection()
    {
        $qtd = 5;
        // Cria alguns pacientes no banco de dados
        Patient::factory()->count($qtd)->create();

        $this->assertEquals($qtd, Patient::count());

        // Faz uma solicitação GET para a rota /patients com o objeto Request simulado
        $response = $this->get('/api/patients');

        // Verifica se a resposta possui o status HTTP 200 (OK)
        $response->assertStatus(200);

        // Verifica se o cabeçalho Content-Type é do tipo application/json
        $response->assertHeader('Content-Type', 'application/json');

        // Verifica se o array "data" na resposta tem o tamanho esperado de 5
        $response->assertJsonCount($qtd, 'data');
    }

    /** @test */
    public function testIndexReturnsErrorMessageWhenNoPatientsFound()
    {
        // Faz uma solicitação GET para a rota /patients quando não há pacientes no banco de dados
        $response = $this->get('/api/patients');

        // Verifica se a resposta possui o status HTTP 404 (Not Found)
        $response->assertStatus(404);

        // Verifica se a resposta contém a mensagem de erro esperada
        $response->assertJson([
            'error' => 'Nenhum paciente encontrado.'
        ]);
    }

    /** @test */
    public function testIndexReturnsPatientCollectionWithCustomPerPageParameter()
    {
        // Cria alguns pacientes no banco de dados
        Patient::factory()->count(15)->create();

        // Faz uma solicitação GET para a rota /patients com o parâmetro per_page definido como 5
        $response = $this->get('/api/patients?per_page=5');

        // Verifica se a resposta possui o status HTTP 200 (OK)
        $response->assertStatus(200);

        // Verifica se a resposta contém uma coleção de pacientes paginados com 5 itens por página
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function testCreatePatientSuccessfully()
    {
        // Dados do paciente para o teste
        $patientData = [
            'photo' => 'https://example.com/photo.jpg',
            'full_name' => 'John Doe',
            'mother_name' => 'Jane Doe',
            'date_of_birth' => '1990-01-01',
            'cpf' => '43298460478',
            'cns' => '212792918470007',
            'address' => [
                'cep' => '75099009',
                'street' => 'Travessa Godói',
                'number' => '5',
                'complement' => 'Bloco C',
                'neighborhood' => 'do Leste',
                'city' => 'Santa Marcelo',
                'state' => 'AL'
            ]
        ];

        // Faz uma solicitação POST para a rota /api/patients/create
        $response = $this->postJson('/api/patients/create', $patientData);

        // Verifica se o código de status HTTP retornado é 201 (Created)
        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Paciente criado com sucesso',
            ]);
    }
}
