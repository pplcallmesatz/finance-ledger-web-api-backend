<?php

namespace Database\Factories;

use App\Models\SalesLedger;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesLedgerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesLedger::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total_product_price' => $this->faker->randomNumber(2),
            'selling_product_price' => $this->faker->randomNumber(2),
            'payment_status' => $this->faker->text(255),
            'remarks' => $this->faker->text(),
            'company_address' => $this->faker->text(),
            'invoice_number' => $this->faker->text(255),
            'payment_method' => $this->faker->text(255),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
