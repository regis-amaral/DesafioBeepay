<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Patient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'photo' => 'https://picsum.photos/2' . rand(10,99),
            'full_name' => $this->faker->name(),
            'mother_name' => $this->faker->name(gender: 'female'),
            'date_of_birth' => $this->faker->dateTimeBetween('-100 years', 'now', null),
            'cpf' => $this->faker->unique()->cpf(),
            'cns' => CNSGenerator::generate()
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Patient $patient) {
            $patient->address()->save(Address::factory()->make());
        });
    }

    protected function withFaker()
    {
        return \Faker\Factory::create('pt_BR');
    }
}
