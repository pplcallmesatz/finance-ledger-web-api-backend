<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\SalesLedger;
use App\Models\Transaction;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesLedgerTransactionsTest extends TestCase
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
    public function it_gets_sales_ledger_transactions(): void
    {
        $salesLedger = SalesLedger::factory()->create();
        $transactions = Transaction::factory()
            ->count(2)
            ->create([
                'sales_ledger_id' => $salesLedger->id,
            ]);

        $response = $this->getJson(
            route('api.sales-ledgers.transactions.index', $salesLedger)
        );

        $response->assertOk()->assertSee($transactions[0]->reason);
    }

    /**
     * @test
     */
    public function it_stores_the_sales_ledger_transactions(): void
    {
        $salesLedger = SalesLedger::factory()->create();
        $data = Transaction::factory()
            ->make([
                'sales_ledger_id' => $salesLedger->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.sales-ledgers.transactions.store', $salesLedger),
            $data
        );

        unset($data['sales_ledger_id']);
        unset($data['expense_ledger_id']);

        $this->assertDatabaseHas('transactions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $transaction = Transaction::latest('id')->first();

        $this->assertEquals($salesLedger->id, $transaction->sales_ledger_id);
    }
}
