<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Transaction;

use App\Models\SalesLedger;
use App\Models\ExpenseLedger;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_transactions(): void
    {
        $transactions = Transaction::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('transactions.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.transactions.index')
            ->assertViewHas('transactions');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_transaction(): void
    {
        $response = $this->get(route('transactions.create'));

        $response->assertOk()->assertViewIs('app.transactions.create');
    }

    /**
     * @test
     */
    public function it_stores_the_transaction(): void
    {
        $data = Transaction::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('transactions.store'), $data);

        unset($data['sales_ledger_id']);
        unset($data['expense_ledger_id']);

        $this->assertDatabaseHas('transactions', $data);

        $transaction = Transaction::latest('id')->first();

        $response->assertRedirect(route('transactions.edit', $transaction));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->get(route('transactions.show', $transaction));

        $response
            ->assertOk()
            ->assertViewIs('app.transactions.show')
            ->assertViewHas('transaction');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->get(route('transactions.edit', $transaction));

        $response
            ->assertOk()
            ->assertViewIs('app.transactions.edit')
            ->assertViewHas('transaction');
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

        $response = $this->put(
            route('transactions.update', $transaction),
            $data
        );

        unset($data['sales_ledger_id']);
        unset($data['expense_ledger_id']);

        $data['id'] = $transaction->id;

        $this->assertDatabaseHas('transactions', $data);

        $response->assertRedirect(route('transactions.edit', $transaction));
    }

    /**
     * @test
     */
    public function it_deletes_the_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->delete(route('transactions.destroy', $transaction));

        $response->assertRedirect(route('transactions.index'));

        $this->assertModelMissing($transaction);
    }
}
