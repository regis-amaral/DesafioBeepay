<?php

namespace App\Services;

use App\Rules\CNS;
use App\Rules\CPF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Fornece métodos estáticos para validar os dados relacionados aos pacientes e retornar mensagens personalizadas.
 *
 * Esta classe contém métodos estáticos para validar os dados enviados nas requisições
 * de inserção e atualização de pacientes. Inclui validações para campos como foto, nome completo,
 * nome da mãe, data de nascimento, endereço, CPF, CNS, etc. As regras de validação são definidas
 * como métodos estáticos para facilitar o acesso e reutilização em outras partes do código.
 *
 * @category Classe
 * @package  App\Services
 */

class PatientValidationService
{

    /**
     * Valida os dados do paciente.
     *
     * Este método recebe um array de dados do paciente e valida esses dados de acordo com as regras de validação
     * definidas na classe. Se os dados não forem válidos, uma exceção é lançada contendo a primeira mensagem de erro
     * encontrada. As mensagens de erro são personalizadas de acordo com as regras definidas no método getMessages.
     *
     * @param array $data Os dados do paciente a serem validados.
     * @return array O array de dados do paciente validado.
     * @throws \InvalidArgumentException Se os dados do paciente não forem válidos.
     */

    public static function validateData(array $data): array
    {
        $validator = Validator::make($data, PatientValidationService::getRules(), PatientValidationService::getMessages());

        if ($validator->fails()) {
            // Obter todas as mensagens de erro
            $messages = $validator->getMessageBag()->all();

            // Concatenar as mensagens em uma string
            $errorMessage = implode("\n", $messages);

            throw new \InvalidArgumentException($errorMessage);
        }

        return $data;
    }

    /**
     * Retorna as regras de validação para os dados do paciente.
     *
     * Este método retorna um array associativo contendo as regras de validação para os diferentes campos dos dados do paciente.
     * As regras são definidas com base nos requisitos de validação, como obrigatoriedade, tipo de dado, tamanho mínimo ou máximo, etc.
     *
     * @param bool $patientId Indica se a verificação de CPF ou CNS deve ignorar o CPF e CNS do usuário verificado
     * @return array Um array associativo contendo as regras de validação para os dados do paciente.
     */

    public static function getRules($patientId = null):array
    {
        return [
            'photo' => 'sometimes|string|url',
            'full_name' => 'required|string|min:5',
            'mother_name' => 'required|string|min:5',
            'date_of_birth' => 'required|date|after_or_equal:' . now()->subYears(150)->format('Y-m-d').'|before:' . now()->format('Y-m-d'),
            'cpf' => ['required', 'string', Rule::unique('patients')->ignore($patientId), new CPF()],
            'cns' => ['required', 'string', Rule::unique('patients')->ignore($patientId), new CNS()],
            'address' => 'required|array',
            'address.cep' => ['required','integer','regex:/^\d{8}$/'],
            'address.street' => 'required|string|min:3|max:255',
            'address.number' => 'required|string|min:1|max:5',
            'address.complement' => 'required|string|min:1|max:255',
            'address.neighborhood' => 'required|string|min:3|max:255',
            'address.city' => 'required|string|min:3|max:255',
            'address.state' => 'required|string|min:2|max:2',
        ];
    }

    /**
     * Retorna as mensagens de erro associadas às regras de validação para os dados do paciente.
     *
     * Este método retorna um array associativo onde as chaves são os nomes das regras de validação
     * e os valores são as mensagens de erro correspondentes.
     * As mensagens são personalizadas para fornecer informações claras sobre o motivo da falha da validação.
     *
     * @return array Um array associativo contendo as mensagens de erro associadas às regras de validação.
     */

    public static function getMessages(): array
    {
        return [
            'photo.string' => 'O campo foto deve ser uma string com a url da imagem',
            'photo.url' => 'O campo foto deve ser uma url válida',

            'full_name.required' => 'O nome completo é obrigatório',
            'full_name.string' => 'O nome completo precisa ser uma string',
            'full_name.min' => 'O nome completo precisa ter no mínimo :min caracteres',

            'mother_name.required' => 'O campo nome da mãe é obrigatório.',
            'mother_name.string' => 'O campo nome da mãe deve ser uma string.',
            'mother_name.min' => 'O campo nome da mãe deve ter no mínimo :min caracteres.',

            'date_of_birth.required' => 'O campo data de nascimento é obrigatório.',
            'date_of_birth.date' => 'O campo data de nascimento deve ser uma data válida.',
            'date_of_birth.after_or_equal' => 'O campo data de nascimento não pode ser anterior a ' . now()->subYears(150)->format('d/m/Y') . '.',
            'date_of_birth.before' => 'O campo data de nascimento não pode ser maior que a data atual.',

            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.string' => 'O campo CPF deve ser uma string.',
            'cpf.unique' => 'Este CPF já está em uso.',

            'cns.required' => 'O campo CNS é obrigatório.',
            'cns.string' => 'O campo CNS deve ser uma string.',
            'cns.unique' => 'Este CNS já está em uso.',

            'address' => 'O campo de endereço é obrigatório.',
            'address.array' => 'O campo de endereço deve ser um array.',

            'address.cep.required' => 'O CEP é obrigatório.',
            'address.cep.integer' => 'O CEP deve ser um inteiro com 8 digitos.',
            'address.cep.regex' => 'O CEP deve ter 8 digitos',

            'address.street.required' => 'O campo de rua é obrigatório.',
            'address.street.string' => 'O campo de rua deve ser uma string.',
            'address.street.min' => 'O campo de rua deve ter pelo menos :min caracteres.',
            'address.street.max' => 'O campo de rua não pode ter mais de :max caracteres.',

            'address.number.required' => 'O campo de número é obrigatório.',
            'address.number.string' => 'O campo de número deve ser uma string.',
            'address.number.min' => 'O campo de número deve ter pelo menos :min caracteres.',
            'address.number.max' => 'O campo de número não pode ter mais de :max caracteres.',

            'address.complement.required' => 'O campo de complemento é obrigatório.',
            'address.complement.string' => 'O campo de complemento deve ser uma string.',
            'address.complement.min' => 'O campo de complemento deve ter pelo menos :min caracteres.',
            'address.complement.max' => 'O campo de complemento não pode ter mais de :max caracteres.',

            'address.neighborhood.required' => 'O campo de bairro é obrigatório.',
            'address.neighborhood.string' => 'O campo de bairro deve ser uma string.',
            'address.neighborhood.min' => 'O campo de bairro deve ter pelo menos :min caracteres.',
            'address.neighborhood.max' => 'O campo de bairro não pode ter mais de :max caracteres.',

            'address.city.required' => 'O campo de cidade é obrigatório.',
            'address.city.string' => 'O campo de cidade deve ser uma string.',
            'address.city.min' => 'O campo de cidade deve ter pelo menos :min caracteres.',
            'address.city.max' => 'O campo de cidade não pode ter mais de :max caracteres.',

            'address.state.required' => 'O campo de estado é obrigatório.',
            'address.state.string' => 'O campo de estado deve ser uma string.',
            'address.state.min' => 'O campo de estado deve ter pelo menos :min caracteres.',
            'address.state.max' => 'O campo de estado não pode ter mais de :max caracteres.',

        ];
    }
}
