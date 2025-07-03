<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'purchase_price' => $this->faker->randomNumber(2),
            'transport_charge' => $this->faker->randomNumber(2),
            'packing_price' => $this->faker->randomNumber(2),
            'product_price' => $this->faker->randomNumber(2),
            'selling_price' => $this->faker->randomNumber(2),
            'description' => $this->faker->sentence(15),
            'category_master_id' => \App\Models\CategoryMaster::factory(),
        ];
    }
}
