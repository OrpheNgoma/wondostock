<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'contact_person' => $this->faker->name,
            'email' => $this->faker->unique()->companyEmail,
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'nif' => $this->faker->unique()->randomNumber(8),
            'rccm' => 'RCCM/' . $this->faker->unique()->randomNumber(8),
            'notes' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
