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
        $response = $this->get('/');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'status' => 'API Online'
        ]);
    }

    /** @test */
    public function testIndexReturnsPatientCollection()
    {
        $qtd = 5;

        Patient::factory()->count($qtd)->create();

        $this->assertEquals($qtd, Patient::count());

        $response = $this->get('/api/patients');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertHeader('Content-Type', 'application/json');

        $response->assertJsonCount($qtd, 'data');
    }

    /** @test */
    public function testIndexReturnsErrorMessageWhenNoPatientsFound()
    {
        $response = $this->get('/api/patients');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson([
            'error' => 'Nenhum paciente encontrado.'
        ]);
    }

    /** @test */
    public function testIndexReturnsPatientCollectionWithCustomPerPageParameter()
    {
        Patient::factory()->count(15)->create();

        $response = $this->get('/api/patients?per_page=5');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function testCreatePatientSuccessfully()
    {
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

        $response = $this->postJson('/api/patients/create', $patientData);

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

        $response = $this->get("/api/patients/{$patient->id}");

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('id', $patient->id)
            ->assertJsonPath('address.id', $patient->address->id);
    }

    /** @test */
    public function testShowReturnsNotFoundForInvalidId()
    {
        $response = $this->get('/api/patients/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson(['message' => 'Paciente não encontrado.']);
    }
    /** @test */
    public function testUpdatePatientSuccessfully()
    {
        $patient = Patient::factory()->create();

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

        $response = $this->putJson("/api/patients/{$patient->id}/update", $updatedData);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(['message' => 'Paciente atualizado com sucesso']);
    }

    /** @test */
    public function testUpdatePatientNotFound()
    {
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

        $response = $this->putJson("/api/patients/0/update", $updatedData);

        $response->assertJson(['error' => 'Paciente não encontrado.']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function testUpdatePatientValidationFailed()
    {
        $patient = Patient::factory()->create();

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

        $response = $this->putJson("/api/patients/{$patient->id}/update", $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('data.cpf', ['O campo CPF não é válido.']);
    }

    /** @test */
    public function testDeletePatient()
    {
        $patient = Patient::factory()->create();

        $this->assertModelExists($patient);

        $response = $this->delete("/api/patients/{$patient->id}/delete");

        $this->assertNull(Cache::get('patient_' . $patient->id));

        $this->assertModelMissing($patient);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
    /** @test */
    public function testNotFoundOnDeletePatient()
    {
        $response = $this->delete("/api/patients/0/delete");

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson(['error' => 'Paciente não encontrado.']);
    }
    /** @test */
    public function testUploadCsvSuccessfully()
    {
        $qtd = 2;

        $patients = Patient::factory()->count($qtd)->create();

        $csvContent = CsvContentGenerator::generatePatientsCsvContent($patients);

        $localFilePath = sys_get_temp_dir() . '/patients';
        file_put_contents($localFilePath, $csvContent);

        $uploadedFile = new UploadedFile($localFilePath, 'patients-valid_file.csv', 'text/csv', null, true);

        $response = $this->postJson('/api/patients/upload-csv', [
            'csv_file' => $uploadedFile,
        ]);

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
