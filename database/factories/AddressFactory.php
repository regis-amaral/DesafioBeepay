<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cep' => $this->faker->randomNumber(8),
            'street' => str_replace(',', '', $this->faker->streetAddress),
            'number' => $this->faker->buildingNumber,
            'complement' => str_replace(',', '', $this->faker->secondaryAddress),
            'neighborhood' => str_replace(',', '', $this->faker->citySuffix),
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr
        ];
    }

    protected function withFaker()
    {
        return \Faker\Factory::create('pt_BR');
    }
}
