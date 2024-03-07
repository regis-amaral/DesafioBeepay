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
        'address', // Endereço
        'number', // Número
        'complement', // Complemento
        'neighborhood', // Bairro
        'city', // Cidade
        'state', // Estado
    ];

    public function patient()
    {
        return $this->hasMany(Patient::class);
    }
}
