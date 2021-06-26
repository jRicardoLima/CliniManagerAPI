<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

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
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'contry' => $this->faker->country(),
            'state' => $this->faker->state(),
            'zipcode' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'neighborhood' => $this->faker->streetAddress(),
            'street' => $this->faker->streetName(),
            'email' => $this->faker->email(),
            'organization_id' => 1
        ];
    }
}
