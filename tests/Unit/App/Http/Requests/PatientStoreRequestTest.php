<?php

namespace Tests\Unit\App\Http\Requests;

use App\Http\Requests\PatientStoreRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PatientStoreRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testValidationPassesWithValidData()
    {
        // Dados válidos para um novo paciente
        $data = [
            'photo' => 'https://example.com/photo.jpg',
            'full_name' => 'John Doe',
            'mother_name' => 'Jane Doe',
            'date_of_birth' => '1990-01-01',
            'cpf' => '80619170255',
            'cns' => '960825804390000',
            'address' => [
                'cep' => '75099009',
                'street' => 'Travessa Godói, 5680',
                'number' => '5',
                'complement' => 'Bloco C',
                'neighborhood' => 'do Leste',
                'city' => 'Santa Marcelo',
                'state' => 'AL'
            ]
        ];

        // Crie uma instância do PatientStoreRequest com os dados válidos
        $request = new PatientStoreRequest($data);

        // Verifique se a autorização passa
        $this->assertTrue($request->authorize());

        // Verifique se as regras de validação estão sendo aplicadas corretamente
        $validator = App::make('validator')->make($data, $request->rules());
        $this->assertFalse($validator->fails());

    }

    /** @test */
    public function testValidationFailsWithInvalidCpf()
    {
        try {
            // Dados inválidos para um novo paciente
            $data = [
                'photo' => 'https://example.com/photo.jpg',
                'full_name' => 'John Doe',
                'mother_name' => 'Jane Doe',
                'date_of_birth' => '1990-01-01',
                'cpf' => '12345678900',
                'cns' => '960825804390000',
                'address' => [
                    'cep' => '75099009',
                    'street' => 'Travessa Godói, 5680',
                    'number' => '5',
                    'complement' => 'Bloco C',
                    'neighborhood' => 'do Leste',
                    'city' => 'Santa Marcelo',
                    'state' => 'AL'
                ]
            ];

            // Crie uma instância do PatientStoreRequest com os dados inválidos
            $request = new PatientStoreRequest($data);

            // Verifique se a autorização passa
            $this->assertTrue($request->authorize());

            // Verifique se as regras de validação estão sendo aplicadas corretamente
            $validator = App::make('validator')->make($data, $request->rules());

            // Criar uma instância de JsonResponse com os dados esperados
            $responseData = [
                'success' => false,
                'message' => 'Validation errors',
                'data' => ['cpf' => ['O campo CPF não é válido.']],
            ];
            $jsonResponse = new JsonResponse($responseData, 422);

            // Valida os dados com a classe de request
            $request->failedValidation($validator);
        } catch (HttpResponseException $exception) {

            // Extrair a resposta JSON da exceção
            $jsonResponse = json_decode($exception->getResponse()->getContent(), true);

            //dd($jsonResponse);

            $this->assertFalse($jsonResponse['success']);

            $this->assertEquals('Validation errors', $jsonResponse['message']);

            $this->assertEquals(['O campo CPF não é válido.'], $jsonResponse['data']['cpf']);
        }
    }

    /** @test */
    public function testRules(){
        $patientStoreRequest = new PatientStoreRequest();

        $this->assertIsArray($patientStoreRequest->rules());

        $this->assertArrayHasKey('cpf',$patientStoreRequest->rules());

    }
    /** @test */
    public function testMessages(){
        $patientStoreRequest = new PatientStoreRequest();

        $this->assertIsArray($patientStoreRequest->messages());

        $this->assertArrayHasKey('cpf.required', $patientStoreRequest->messages());

        $this->assertEquals('O campo CPF é obrigatório.', $patientStoreRequest->messages()["cpf.required"]);
    }
}
