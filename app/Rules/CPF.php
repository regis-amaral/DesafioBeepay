<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Regra de validação para o CPF (Cadastro de Pessoas Físicas).
 *
 * Esta classe implementa a interface Rule do Laravel e define a lógica para validar se um número de CPF é válido.
 * O número de CPF é validado de acordo com o algoritmo de cálculo do dígito verificador.
 * A validação inclui verificação do formato do CPF, cálculo e validação dos dígitos verificadores.
 *
 * @category Classe
 * @package  App\Rules
 */
class CPF implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Remove todos os caracteres que não sejam dígitos
        $cpf = preg_replace('/[^0-9]/', '', $value);

        // Verifica se o CPF tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int)($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : (11 - $remainder);

        // Calcula o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int)($cpf[$i]) * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : (11 - $remainder);

        // Verifica se os dígitos verificadores estão corretos
        return ($cpf[9] == $digit1 && $cpf[10] == $digit2);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O campo CPF não é válido.';
    }
}
