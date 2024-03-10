<?php

namespace Tests\Helpers;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class CsvContentGenerator
{
    /**
     * Retorna um arquivo csv para testes
     * @param array|null $patients
     * @param $quantity
     * @return string
     */
    public static function generatePatientsCsvContent(Collection $patients = null, $quantity = 2): string
    {
        $patients = $patients ?? Patient::factory()->count($quantity)->create();

        $csvData = "photo,full_name,mother_name,date_of_birth,cpf,cns,address.cep,address.street,address.number,address.complement,address.neighborhood,address.city,address.state\n";

        foreach ($patients as $patient) {
            $dateOfBirth = $patient->date_of_birth->format('Y-m-d'); // Formata a data de nascimento
            $csvData .= "{$patient->photo},{$patient->full_name},{$patient->mother_name},{$dateOfBirth},{$patient->cpf},{$patient->cns},{$patient->address->cep},{$patient->address->street},{$patient->address->number},{$patient->address->complement},{$patient->address->neighborhood},{$patient->address->city},{$patient->address->state}\n";
        }

        return $csvData;
    }
}
