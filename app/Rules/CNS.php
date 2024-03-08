<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CNS implements Rule
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
        $cns = preg_replace('/[^0-9]/', '', $value);

        // Verifica se o CNS tem 15 dígitos
        if (strlen($cns) != 15) {
            return false;
        }

        $pis = substr($cns, 0, 11);

        // Rotina de validação de Números que iniciam com 1 ou 2
        if (substr($pis, 0, 1) == '1' || substr($pis, 0, 1) == '2') {

            $soma = (int)$pis[0] * 15 +
                (int)$pis[1] * 14 +
                (int)$pis[2] * 13 +
                (int)$pis[3] * 12 +
                (int)$pis[4] * 11 +
                (int)$pis[5] * 10 +
                (int)$pis[6] * 9 +
                (int)$pis[7] * 8 +
                (int)$pis[8] * 7 +
                (int)$pis[9] * 6 +
                (int)$pis[10] * 5;

            $resto = $soma % 11;
            $dv = 11 - $resto;

            if ($dv == 11) {
                $dv = 0;
            }

            if ($dv == 10) {
                $soma += 2;
                $resto = $soma % 11;
                $dv = 11 - $resto;
                $resultado = $pis . "001" . $dv;
            } else {
                $resultado = $pis . "000" . $dv;
            }

            if ($cns != $resultado) {
                return false;
            } else {
                return true;
            }
        }

        // Rotina de validação de Números que iniciam com 7, 8 ou 9
        if (in_array(substr($cns, 0, 1), ['7', '8', '9'])) {
            $soma = (int)$cns[0] * 15 +
                (int)$cns[1] * 14 +
                (int)$cns[2] * 13 +
                (int)$cns[3] * 12 +
                (int)$cns[4] * 11 +
                (int)$cns[5] * 10 +
                (int)$cns[6] * 9 +
                (int)$cns[7] * 8 +
                (int)$cns[8] * 7 +
                (int)$cns[9] * 6 +
                (int)$cns[10] * 5 +
                (int)$cns[11] * 4 +
                (int)$cns[12] * 3 +
                (int)$cns[13] * 2 +
                (int)$cns[14] * 1;

            $resto = $soma % 11;

            if ($resto != 0){
                return false;
            }
            else{
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O campo CNS não é válido.';
    }
}
