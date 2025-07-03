<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Transaction;

use App\Models\SalesLedger;
use App\Models\ExpenseLedger;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_transactions_list(): void
    {
        $transactions = Transaction::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.transactions.index'));

        $response->assertOk()->assertSee($transactions[0]->reason);
    }

    /**
     * @test
     */
    public function it_stores_the_transaction(): void
    {
        $data = Transaction::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.transactions.store'), $data);

        unset($data['sales_ledger_id']);
        unset($data['expense_ledger_id']);

        $this->assertDatabaseHas('transactions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $salesLedger = SalesLedger::factory()->create();
        $expenseLedger = ExpenseLedger::factory()->create();

        $data = [
            'bank_balance' => $this->faker->randomNumber(2),
            'cash_in_hand' => $this->faker->randomNumber(2),
            'reason' => $this->faker->sentence(15),
            'sales_ledger_id' => $salesLedger->id,
            'expense_ledger_id' => $expenseLedger->id,
        ];

        $response = $this->putJson(
            route('api.transactions.update', $transaction),
            $data
        );

        unset($data['sales_ledger_id']);
        unset($data['expense_ledger_id']);

        $data['id'] = $transaction->id;

        $this->assertDatabaseHas('transactions', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->deleteJson(
            route('api.transactions.destroy', $transaction)
        );

        $this->assertModelMissing($transaction);

        $response->assertNoContent();
    }
}
