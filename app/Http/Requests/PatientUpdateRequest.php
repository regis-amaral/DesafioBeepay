<?php

namespace App\Http\Requests;

use App\Services\PatientValidationService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Classe para lidar com a validação de dados ao atualizar um paciente existente.
 *
 * Esta classe estende a classe FormRequest do Laravel e é usada para validar
 * os dados fornecidos ao atualizar um paciente existente. Ela define as regras de validação
 * para os campos do paciente que podem ser atualizados, como nome, data de nascimento, CPF, CNS, etc.
 *
 * @category Classe
 * @package  App\Http\Requests
 */
class PatientUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return PatientValidationService::getRules($this->id);
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], 422));
    }

    public function messages(): array
    {
        return PatientValidationService::getMessages();
    }
}
