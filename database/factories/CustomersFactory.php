<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customers>
 */
class CustomersFactory extends Factory
{
    protected $model = \App\Models\Customers::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'bandwidth' => $this->faker->randomNumber(2) . ' Mbps',
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'ip' => $this->faker->ipv4(),
            'sub_district' => $this->faker->city(),
            'urban_village' => $this->faker->citySuffix(),
            'subscribe' => $this->faker->dateTimeBetween('-2 year', 'now')->format('Y-m'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
