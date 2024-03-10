<?php

namespace Tests\Unit\App\Models;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function testPatientCanBeCreated()
    {
        $patient = Patient::factory()->create([
            'photo' => 'https://example.com/photo.jpg',
            'full_name' => 'John Doe',
            'mother_name' => 'Jane Doe',
            'date_of_birth' => '1990-01-01',
            'cpf' => '80619170255',
            'cns' => '960825804390000',
        ]);

        $this->assertInstanceOf(Patient::class, $patient);
        $this->assertEquals('John Doe', $patient->full_name);
        $this->assertEquals('Jane Doe', $patient->mother_name);
        $this->assertEquals('1990-01-01', $patient->date_of_birth);
        $this->assertEquals('80619170255', $patient->cpf);
        $this->assertEquals('960825804390000', $patient->cns);
    }

    /** @test */
    public function testItHasAnAddressRelationship()
    {
        $patient = Patient::factory()->create();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasOne', $patient->address());
    }
}
