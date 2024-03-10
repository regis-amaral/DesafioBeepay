<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Tests\Helpers\CsvContentGenerator;
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
        $response->assertStatus(Response::HTTP_OK);

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
        $response->assertStatus(Response::HTTP_OK);

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
        $response->assertStatus(Response::HTTP_NOT_FOUND);

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
        $response->assertStatus(Response::HTTP_OK);

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
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Paciente criado com sucesso',
            ]);
    }
    /** @test */
    public function testShowReturnsPatientResource()
    {
        $patient = Patient::factory()->create();

        // Faz uma solicitação GET para a rota /api/patients/{id} com o ID do paciente criado
        $response = $this->get("/api/patients/{$patient->id}");

        // Compara o retorno
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('id', $patient->id)
            ->assertJsonPath('address.id', $patient->address->id);
    }

    /** @test */
    public function testShowReturnsNotFoundForInvalidId()
    {
        // Faz uma solicitação GET para a rota /api/patients/999 (ID inválido)
        $response = $this->get('/api/patients/0');

        // Verifica se a resposta possui o status HTTP 404 (Not Found)
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        // Verifica se a resposta contém a mensagem de erro correta
        $response->assertJson(['message' => 'Paciente não encontrado.']);
    }
    /** @test */
    public function testUpdatePatientSuccessfully()
    {
        // Criar um paciente no banco de dados
        $patient = Patient::factory()->create();

        // Dados atualizados para o paciente
        $updatedData = [
            'id' => $patient->id,
            'photo' => 'https://picsum.photos/201',
            'full_name' => 'Jose Alfredo',
            'mother_name' => 'Maria Madalena',
            'date_of_birth' => '1985-05-10',
            'cpf' => $patient->cpf,
            'cns' => $patient->cns,
            'address' => [
                'id' => $patient->address->id,
                'cep' => '97985231',
                'street' => 'Rua das Graças',
                'number' => '123',
                'complement' => 'Apto 23',
                'neighborhood' => 'Belo Monte',
                'city' => 'Osasco',
                'state' => 'SP',
            ],
        ];

        // Fazer uma solicitação PUT para a rota /api/patients/{$patient->id}/update com os dados atualizados
        $response = $this->putJson("/api/patients/{$patient->id}/update", $updatedData);

        // Verificar se o código de status HTTP retornado é 201 (Created)
        $response->assertStatus(Response::HTTP_CREATED);

        // Verifica se a resposta contém a mensagem correta
        $response->assertJson(['message' => 'Paciente atualizado com sucesso']);
    }

    /** @test */
    public function testUpdatePatientNotFound()
    {
        // Dados atualizados para o paciente
        $updatedData = [
            'id' => 0,
            'photo' => 'https://picsum.photos/201',
            'full_name' => 'Jose Alfredo',
            'mother_name' => 'Maria Madalena',
            'date_of_birth' => '1985-05-10',
            'cpf' => '43298460478',
            'cns' => '212792918470007',
            'address' => [
                'id' => 0,
                'cep' => '97985231',
                'street' => 'Rua das Graças',
                'number' => '123',
                'complement' => 'Apto 23',
                'neighborhood' => 'Belo Monte',
                'city' => 'Osasco',
                'state' => 'SP',
            ],
        ];
        // Fazer uma solicitação PUT para um ID de paciente inexistente
        $response = $this->putJson("/api/patients/0/update", $updatedData);

        // Verificar se a resposta JSON contém a mensagem de erro
        $response->assertJson(['error' => 'Paciente não encontrado.']);

        // Verificar se o código de status HTTP retornado é 404 (Not Found)
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function testUpdatePatientValidationFailed()
    {
        // Criar um paciente no banco de dados
        $patient = Patient::factory()->create();

        // Dados inválidos para atualização do paciente (CPF inválido)
        $invalidData = [
            'id' => $patient->id,
            'photo' => 'https://picsum.photos/201',
            'full_name' => 'Jose Alfredo',
            'mother_name' => 'Maria Madalena',
            'date_of_birth' => '1985-05-10',
            'cpf' => '00011122233',
            'cns' => $patient->cns,
            'address' => [
                'id' => 0,
                'cep' => '97985231',
                'street' => 'Rua das Graças',
                'number' => '123',
                'complement' => 'Apto 23',
                'neighborhood' => 'Belo Monte',
                'city' => 'Osasco',
                'state' => 'SP',
            ],
        ];

        // Fazer uma solicitação PUT para a rota /api/patients/{$patient->id}/update com dados inválidos
        $response = $this->putJson("/api/patients/{$patient->id}/update", $invalidData);

        // Verificar se o código de status HTTP retornado é 422 (Unprocessable Entity)
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Verificar se a resposta JSON contém a mensagem de erro
        $response->assertJsonPath('data.cpf', ['O campo CPF não é válido.']);
    }

    /** @test */
    public function testDeletePatient()
    {
        // Crie um paciente para deletar
        $patient = Patient::factory()->create();

        // Verifique se o paciente existe no banco de dados antes da deleção
        $this->assertModelExists($patient);

        // Faça uma solicitação DELETE para a rota de exclusão do paciente
        $response = $this->delete("/api/patients/{$patient->id}/delete");

        // Verifique se o paciente foi removido do cache
        $this->assertNull(Cache::get('patient_' . $patient->id));

        // Verifique se o paciente não existe mais no banco de dados após a deleção
        $this->assertModelMissing($patient);

        // Verifique se o código de status HTTP retornado é 204 (No Content)
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
    /** @test */
    public function testNotFoundOnDeletePatient()
    {
        // Faça uma solicitação DELETE para a rota de exclusão do paciente
        $response = $this->delete("/api/patients/0/delete");

        // Verifique se o código de status HTTP retornado é 404 (Not Found)
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson(['error' => 'Paciente não encontrado.']);
    }
    /** @test */
    public function testUploadCsvSuccessfully()
    {
        $qtd = 2;

        //Cria alguns pacientes para gerar o conteúdo do CSV
        $patients = Patient::factory()->count($qtd)->create();

        // Gera o conteúdo do arquivo CSV com base nos pacientes criados
        $csvContent = CsvContentGenerator::generatePatientsCsvContent($patients);

        // Salva o conteúdo em um arquivo temporário
        $localFilePath = sys_get_temp_dir() . '/patients';
        file_put_contents($localFilePath, $csvContent);

        // Cria um objeto UploadedFile a partir do arquivo temporário
        $uploadedFile = new UploadedFile($localFilePath, 'patients-valid_file.csv', 'text/csv', null, true);

        // Faz a solicitação POST com o arquivo CSV
        $response = $this->postJson('/api/patients/upload-csv', [
            'csv_file' => $uploadedFile,
        ]);

        // Verifica se a quantidade de pregistros de pacientes foi gravada no banco
        $this->assertEquals($qtd, Patient::count());

        //$response->assertStatus(Response::HTTP_OK);

    }

    /** @test */
    public function testUploadCsvNoFileSent()
    {
        $response = $this->postJson('/api/patients/upload-csv');

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'error' => 'Nenhum arquivo foi enviado.',
            ]);
    }

    /** @test */
    public function testUploadCsvInvalidFileType()
    {
        $file = UploadedFile::fake()->create('patients.jpg', 500); //

        $response = $this->postJson('/api/patients/upload-csv', [
            'csv_file' => $file,
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'error' => 'O arquivo enviado não é um CSV válido.',
            ]);
    }

    /** @test */
    public function testUploadCsvExceedsFileSizeLimit()
    {
        $file = UploadedFile::fake()->create('patients.csv', 11000); // Cria um arquivo fake de 11MB

        $response = $this->postJson('/api/patients/upload-csv', [
            'csv_file' => $file,
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'error' => 'O tamanho do arquivo excede o limite máximo permitido.',
            ]);
    }

    /** @test */
    public function searchByCpfReturnsCorrectPatient()
    {
        $patients = Patient::factory()->count(100)->create();

        $patient = $patients[1];

        $response = $this->getJson('/api/patients/search/?cpf=' . $patient->cpf);

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function searchByCnsReturnsCorrectPatient()
    {
        $patients = Patient::factory()->count(100)->create();

        $patient = $patients[1];

        $response = $this->getJson('/api/patients/search/?cns=' . $patient->cns);

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function searchByFullNameReturnsCorrectPatient()
    {
        $patients = Patient::factory()->count(100)->create();

        $patient = $patients[1];

        $response = $this->getJson('/api/patients/search/?full_name=' . $patient->full_name);

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function searchByMotherNameReturnsCorrectPatient()
    {
        $patients = Patient::factory()->count(100)->create();

        $patient = $patients[1];

        $response = $this->getJson('/api/patients/search/?mother_name=' . $patient->mother_name);

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function searchByStateReturnsPatients()
    {
        $patients = Patient::factory()->count(100)->create();

        $patient = $patients[1];

        $response = $this->getJson('/api/patients/search/?cep=' . $patient->cep);

        $response->assertStatus(200);

    }
}
