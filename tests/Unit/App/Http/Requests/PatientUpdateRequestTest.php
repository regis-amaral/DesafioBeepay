<?php

namespace Tests\Unit\App\Http\Requests;

use App\Http\Requests\PatientUpdateRequest;
use App\Models\Address;
use App\Models\Patient;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PatientUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    private $patientData = [
        'photo' => 'https://example.com/photo.jpg',
        'full_name' => 'John Doe',
        'mother_name' => 'Jane Doe',
        'date_of_birth' => '1990-01-01',
        'cpf' => '80619170255',
        'cns' => '960825804390000',
    ];

    protected $addressData = [
        'cep' => '75099009',
        'street' => 'Travessa Godói, 5680',
        'number' => '5',
        'complement' => 'Bloco C',
        'neighborhood' => 'do Leste',
        'city' => 'Santa Marcelo',
        'state' => 'AL'
    ];

    /** @test */
    public function testValidationPassesWithValidData()
    {

        $patient = new Patient($this->patientData);
        $patient->save();

        $this->addressData['patient_id'] = $patient->id;
        $address = new Address($this->addressData);
        $address->save();

        // Monta conjunto de dados para validar
        $newValidData = $this->patientData;
        $newValidData['id'] = $address->id;
        $newValidData['address'] = $this->addressData;

        // altera alguns campos
        $newValidData['full_name'] = 'Joselito';
        $newValidData['address']['street'] = 'Rua das Palmeiras';

        // Crie uma instância do PatientStoreRequest com os dados válidos
        $request = new PatientUpdateRequest($newValidData);

        // Verifique se a autorização passa
        $this->assertTrue($request->authorize());

        $validator = App::make('validator')->make($newValidData, $request->rules());
        $this->assertFalse($validator->fails());

    }
    /** @test */
    public function testValidationPassesWithInvalidData()
    {
        try {
            $patient = new Patient($this->patientData);
            $patient->save();

            $this->addressData['patient_id'] = $patient->id;
            $address = new Address($this->addressData);
            $address->save();

            // Monta conjunto de dados para validar
            $newValidData = $this->patientData;
            $newValidData['id'] = $address->id;
            $newValidData['address'] = $this->addressData;

            // altera alguns campos com dados inválidos
            $newValidData['cpf'] = '11122233345';
            $newValidData['address']['cep'] = '123456789';

            // Crie uma instância do PatientStoreRequest com os dados válidos
            $request = new PatientUpdateRequest($newValidData);

            // Verifique se a autorização passa
            $this->assertTrue($request->authorize());

            // Verifique se as regras de validação estão sendo aplicadas corretamente
            $validator = App::make('validator')->make($newValidData, $request->rules());

            // Testa se a validação falha
            $this->assertTrue($validator->fails());

            // Criar uma instância de JsonResponse com os dados esperados
            $responseData = [
                'success' => false,
                'message' => 'Validation errors',
                'data' => ['cpf' => ['O campo CPF não é válido.']],
            ];
            $jsonResponse = new JsonResponse($responseData, 422);

            // Valida os dados com a classe de request
            $request->failedValidation($validator);
        }catch (HttpResponseException $exception) {

            // Extrair a resposta JSON da exceção
            $jsonResponse = json_decode($exception->getResponse()->getContent(), true);

//            dd($jsonResponse);

            $this->assertFalse($jsonResponse['success']);

            $this->assertArrayHasKey('cpf', $jsonResponse['data']);

            $this->assertArrayHasKey('address.cep', $jsonResponse['data']);
        }

    }
    /** @test */
    public function testRules(){
        $patientStoreRequest = new PatientUpdateRequest();

        $this->assertIsArray($patientStoreRequest->rules());

        $this->assertArrayHasKey('cpf',$patientStoreRequest->rules());

    }
    /** @test */
    public function testMessages(){
        $patientStoreRequest = new PatientUpdateRequest();

        $this->assertIsArray($patientStoreRequest->messages());

        $this->assertArrayHasKey('cpf.required', $patientStoreRequest->messages());

        $this->assertEquals('O campo CPF é obrigatório.', $patientStoreRequest->messages()["cpf.required"]);
    }
}
