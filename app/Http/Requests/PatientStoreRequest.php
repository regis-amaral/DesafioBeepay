<?php

namespace App\Http\Requests;

use App\Http\Requests\ValidationMessages\ValidationMessages;
use App\Rules\CnsRule;
use App\Rules\CpfRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
        return [
            'photo' => 'string|min:3|max:100',
            'full_name' => '|string|min:10',
            'mother_name' => 'required|string|min:10',
            'date_of_birth' => 'required|date|after_or_equal:' . now()->subYears(150)->format('Y-m-d').'|before:' . now()->format('Y-m-d'),
            'cpf' => ['required','string','unique:patients',new CpfRule()],
            'cns' => ['required','string','unique:patients',new CnsRule()],

            'address' => 'sometimes|required|array',
            'address.cep' => ['sometimes','required','integer','regex:/^\d{8}$/'],
            'address.street' => 'sometimes|required|string|min:3|max:255',
            'address.number' => 'sometimes|required|string|min:1|max:10',
            'address.complement' => 'string|max:255',
            'address.neighborhood' => 'sometimes|required|string|min:3|max:255',
            'address.city' => 'sometimes|required|string|min:3|max:255',
            'address.state' => 'sometimes|required|string|min:2|max:2',
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
        return ValidationMessages::patientRequestMessages();
    }
}
