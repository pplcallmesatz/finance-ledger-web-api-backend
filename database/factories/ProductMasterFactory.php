<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\ProductMaster;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductMasterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductMaster::class;

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
            'purchase_date' => $this->faker->date(),
            'manufacturing_date' => $this->faker->date(),
            'transportation_cost' => $this->faker->randomNumber(2),
            'invoice_number' => $this->faker->text(255),
            'vendor' => $this->faker->text(),
            'quantity_purchased' => $this->faker->randomNumber(0),
            'batch_number' => $this->faker->text(255),
            'category_id' => \App\Models\CategoryMaster::factory(),
        ];
    }
}
