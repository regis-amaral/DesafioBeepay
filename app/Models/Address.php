<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'cep', // CEP
        'street', // Rua
        'number', // Número
        'complement', // Complemento
        'neighborhood', // Bairro
        'city', // Cidade
        'state', // Estado
        'patient_id'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
