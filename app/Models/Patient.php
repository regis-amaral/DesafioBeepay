<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'photo', // Foto do Paciente
        'full_name', // Nome Completo do Paciente
        'mother_name', // Nome Completo da Mãe
        'date_of_birth', // Data de Nascimento
        'cpf', // CPF
        'cns', // CNS (Cartão Nacional de Saúde)
    ];

    public function address()
    {
        return $this->hasOne(Address::class);
    }

}
