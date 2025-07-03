<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Transaction;
use App\Models\ExpenseLedger;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseLedgerTransactionsTest extends TestCase
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
    public function it_gets_expense_ledger_transactions(): void
    {
        $expenseLedger = ExpenseLedger::factory()->create();
        $transactions = Transaction::factory()
            ->count(2)
            ->create([
                'expense_ledger_id' => $expenseLedger->id,
            ]);

        $response = $this->getJson(
            route('api.expense-ledgers.transactions.index', $expenseLedger)
        );

        $response->assertOk()->assertSee($transactions[0]->reason);
    }

    /**
     * @test
     */
    public function it_stores_the_expense_ledger_transactions(): void
    {
        $expenseLedger = ExpenseLedger::factory()->create();
        $data = Transaction::factory()
            ->make([
                'expense_ledger_id' => $expenseLedger->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.expense-ledgers.transactions.store', $expenseLedger),
            $data
        );

        unset($data['sales_ledger_id']);
        unset($data['expense_ledger_id']);

        $this->assertDatabaseHas('transactions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $transaction = Transaction::latest('id')->first();

        $this->assertEquals(
            $expenseLedger->id,
            $transaction->expense_ledger_id
        );
    }
}
