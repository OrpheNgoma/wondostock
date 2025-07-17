<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purchasePrice = $this->faker->numberBetween(1000, 50000);
        $sellingPrice = $purchasePrice * $this->faker->randomFloat(2, 1.2, 2.0);

        return [
            'name' => $this->faker->words(3, true),
            'sku' => 'SKU-'.$this->faker->unique()->randomNumber(6),
            'description' => $this->faker->paragraph(),
            'purchase_price' => $purchasePrice,
            'selling_price' => $sellingPrice,
            // 'unit' => 'pièce',
            'is_active' => true,
        ];
    }
}
