<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_balance' => $this->faker->randomNumber(2),
            'cash_in_hand' => $this->faker->randomNumber(2),
            'reason' => $this->faker->sentence(15),
            'sales_ledger_id' => \App\Models\SalesLedger::factory(),
            'expense_ledger_id' => \App\Models\ExpenseLedger::factory(),
        ];
    }
}
