<?php

namespace Database\Factories;

use App\Rules\CNS;

class CNSGenerator
{
    /**
     * Generate a valid CNS number.
     *
     * @return string
     */
    public static function generate()
    {
        $rule = new CNS();


        while (true) {
            $gera0 = mt_rand(1, 3);
            if ($gera0 == 3) {
                $gera0 = mt_rand(7, 9);
            }

            $gera1 = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $gera2 = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

            $cns = $gera0 . substr($gera1, -5) . substr($gera2, -5);

            $soma = ((int)$cns[0] * 15) +
                ((int)$cns[1] * 14) +
                ((int)$cns[2] * 13) +
                ((int)$cns[3] * 12) +
                ((int)$cns[4] * 11) +
                ((int)$cns[5] * 10) +
                ((int)$cns[6] * 9) +
                ((int)$cns[7] * 8) +
                ((int)$cns[8] * 7) +
                ((int)$cns[9] * 6) +
                ((int)$cns[10] * 5);

            $resto = $soma % 11;
            $dv = 11 - $resto;
            $dv = ($dv == 11) ? 0 : $dv;

            if ($dv == 10) {
                $soma += 2;
                $resto = $soma % 11;
                $dv = 11 - $resto;
                $cns .= '001' . $dv;
            } else {
                $cns .= '000' . $dv;
            }

            if (strlen($cns) == 15 && $rule->passes('cns', $cns)) {
                return $cns;
            }
        }
    }
}

