<?php

namespace Tests\Unit\App\Models;

use App\Models\Address;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testAddressCanBeCreated()
    {
        // Cria um paciente para associar ao endereço
        $patient = Patient::factory()->create();

        $address = new Address();
        $address->fill([
            'cep' => '75099009',
            'street' => 'Travessa Godói, 5680',
            'number' => '5',
            'complement' => 'Bloco C',
            'neighborhood' => 'do Leste',
            'city' => 'Santa Marcelo',
            'state' => 'AL',
            'patient_id' => $patient->id
        ]);

        // Asserts para os campos de endereço
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('75099009', $address->cep);
        $this->assertEquals('Travessa Godói, 5680', $address->street);
        $this->assertEquals('5', $address->number);
        $this->assertEquals('Bloco C', $address->complement);
        $this->assertEquals('do Leste', $address->neighborhood);
        $this->assertEquals('Santa Marcelo', $address->city);
        $this->assertEquals('AL', $address->state);
        $this->assertEquals($patient->id, $address->patient_id);
    }

    /** @test */
    public function testItBelongsToAPatient()
    {
        // Cria um paciente para associar ao endereço
        $patient = Patient::factory()->create();

        // Cria um endereço associado ao paciente
        $address = Address::factory()->create([
            'patient_id' => $patient->id
        ]);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\belongsTo', $address->patient());
    }
}
