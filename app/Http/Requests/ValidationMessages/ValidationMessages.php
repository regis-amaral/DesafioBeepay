<?php

namespace App\Http\Requests\ValidationMessages;

class ValidationMessages
{
    public static function patientRequestMessages(): array
    {
        return [
            'photo.string' => 'O campo foto deve ser uma string',
            'photo.url' => 'O campo foto deve ser uma url válida',

            'full_name.required' => 'O nome completo é obrigatório',
            'full_name.string' => 'O nome completo precisa ser uma string',
            'full_name.min' => 'O nome completo precisa ter no mínimo 10 caracteres',

            'mother_name.required' => 'O campo nome da mãe é obrigatório.',
            'mother_name.string' => 'O campo nome da mãe deve ser uma string.',
            'mother_name.min' => 'O campo nome da mãe deve ter no mínimo :min caracteres.',

            'date_of_birth.required' => 'O campo data de nascimento é obrigatório.',
            'date_of_birth.date' => 'O campo data de nascimento deve ser uma data válida.',
            'date_of_birth.after_or_equal' => 'O campo data de nascimento não pode ser anterior a ' . now()->subYears(150)->format('d/m/Y') . '.',
            'date_of_birth.before' => 'O campo data de nascimento não pode ser maior que a data atual.',

            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.string' => 'O campo CPF deve ser uma string.',
            'cpf.cpf' => 'O campo CPF deve ser um CPF válido.',
            'cpf.unique' => 'Este CPF já está em uso.',

            'cns.required' => 'O campo CNS é obrigatório.',
            'cns.string' => 'O campo CNS deve ser uma string.',
            'cns.cns' => 'O campo CNS deve ser um CNS válido.',
            'cns.unique' => 'Este CNS já está em uso.',

            'address' => 'O campo de endereço é obrigatório quando fornecido.',

            'address.cep.required' => 'O CEP é obrigatório quando o endereço é fornecido.',
            'address.cep.string' => 'O CEP deve ser uma string.',
            'address.cep.min' => 'O CEP deve ter pelo menos :min caracteres.',
            'address.cep.max' => 'O CEP não pode ter mais de :max caracteres.',

            'address.street.required' => 'O campo de rua é obrigatório quando o endereço é fornecido.',
            'address.street.string' => 'O campo de rua deve ser uma string.',
            'address.street.min' => 'O campo de rua deve ter pelo menos :min caracteres.',
            'address.street.max' => 'O campo de rua não pode ter mais de :max caracteres.',

            'address.number.required' => 'O campo de número é obrigatório quando o endereço é fornecido.',
            'address.number.string' => 'O campo de número deve ser uma string.',
            'address.number.min' => 'O campo de número deve ter pelo menos :min caracteres.',
            'address.number.max' => 'O campo de número não pode ter mais de :max caracteres.',

            'address.complement.string' => 'O campo de complemento deve ser uma string.',
            'address.complement.max' => 'O campo de complemento não pode ter mais de :max caracteres.',

            'address.neighborhood.required' => 'O campo de bairro é obrigatório quando o endereço é fornecido.',
            'address.neighborhood.string' => 'O campo de bairro deve ser uma string.',
            'address.neighborhood.min' => 'O campo de bairro deve ter pelo menos :min caracteres.',
            'address.neighborhood.max' => 'O campo de bairro não pode ter mais de :max caracteres.',

            'address.city.required' => 'O campo de cidade é obrigatório quando o endereço é fornecido.',
            'address.city.string' => 'O campo de cidade deve ser uma string.',
            'address.city.min' => 'O campo de cidade deve ter pelo menos :min caracteres.',
            'address.city.max' => 'O campo de cidade não pode ter mais de :max caracteres.',

            'address.state.required' => 'O campo de estado é obrigatório quando o endereço é fornecido.',
            'address.state.string' => 'O campo de estado deve ser uma string.',
            'address.state.min' => 'O campo de estado deve ter pelo menos :min caracteres.',
            'address.state.max' => 'O campo de estado não pode ter mais de :max caracteres.',

        ];
    }
}
