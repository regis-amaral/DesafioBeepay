<?php

namespace App\Http\Requests;

use App\Http\Requests\Validation\ValidationPatientMessages;
use App\Http\Requests\Validation\ValidationPatientRules;
use App\Rules\CNS;
use App\Rules\CPF;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Classe para lidar com a validação de dados ao criar um novo paciente.
 *
 * Esta classe estende a classe FormRequest do Laravel e é usada para validar
 * os dados fornecidos ao criar um novo paciente. Ele define as regras de validação
 * para os campos do paciente, como nome, data de nascimento, CPF, CNS, etc.
 *
 * @category Classe
 * @package  App\Http\Requests
 */
class PatientStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return ValidationPatientRules::patientRequestRules() + [
                'cpf' => ['required','string','unique:patients',new CPF()],
                'cns' => ['required','string','unique:patients',new CNS()],
            ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], 422));
    }

    public function messages()
    {
        return ValidationPatientMessages::patientRequestMessages();
    }
}
