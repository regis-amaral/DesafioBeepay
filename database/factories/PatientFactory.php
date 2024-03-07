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
            'full_name' => $this->faker->name(),
            'mother_name' => $this->faker->name(gender: 'female'),
            'date_of_birth' => $this->faker->dateTimeBetween('-100 years', 'now', null),
            'cpf' => $this->faker->unique()->cpf(),
            'cns' => $this->faker->unique()->buildingNumber(),
            'address_id' => Address::factory()->create()->id,
        ];
    }
}
