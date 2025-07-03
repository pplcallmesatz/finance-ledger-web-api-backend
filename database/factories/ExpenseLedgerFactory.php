<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\ExpenseLedger;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseLedgerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExpenseLedger::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(15),
            'invoice_number' => $this->faker->text(255),
            'purchase_price' => $this->faker->randomNumber(2),
            'seller' => $this->faker->text(),
            'purchase_date' => $this->faker->date(),
            'payment_method' => $this->faker->text(255),
        ];
    }
}
