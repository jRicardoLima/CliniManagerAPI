<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'cpf_cnpj' => $this->faker->cpf(false),
            'company_name' => $this->faker->company(),
            'fantasy_name' => $this->faker->companySuffix(),
            'address_id' => rand(51,153),
            'organization_id' => 1
        ];
    }
}
