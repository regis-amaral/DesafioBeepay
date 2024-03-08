<?php

namespace App\Http\Requests\Validation;

use App\Rules\CNS;
use App\Rules\CPF;

class ValidationPatientRules
{
    public static function patientRequestRules(): array
    {
        return [
            'photo' => 'string|url',
            'full_name' => '|string|min:10',
            'mother_name' => 'required|string|min:10',
            'date_of_birth' => 'required|date|after_or_equal:' . now()->subYears(150)->format('Y-m-d').'|before:' . now()->format('Y-m-d'),

            'address' => 'sometimes|array',
            'address.cep' => ['required_with:address','required','integer','regex:/^\d{8}$/'],
            'address.street' => 'required_with:address|string|min:3|max:255',
            'address.number' => 'required_with:address|string|min:1|max:10',
            'address.complement' => 'required_with:address|string|max:255',
            'address.neighborhood' => 'required_with:address|sometimes|required|string|min:3|max:255',
            'address.city' => 'required_with:address|string|min:3|max:255',
            'address.state' => 'required_with:address|string|min:2|max:2',
        ];
    }
}
