<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\ExpenseLedger;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseLedgerControllerTest extends TestCase
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
    public function it_displays_index_view_with_expense_ledgers(): void
    {
        $expenseLedgers = ExpenseLedger::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('expense-ledgers.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.expense_ledgers.index')
            ->assertViewHas('expenseLedgers');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_expense_ledger(): void
    {
        $response = $this->get(route('expense-ledgers.create'));

        $response->assertOk()->assertViewIs('app.expense_ledgers.create');
    }

    /**
     * @test
     */
    public function it_stores_the_expense_ledger(): void
    {
        $data = ExpenseLedger::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('expense-ledgers.store'), $data);

        $this->assertDatabaseHas('expense_ledgers', $data);

        $expenseLedger = ExpenseLedger::latest('id')->first();

        $response->assertRedirect(
            route('expense-ledgers.edit', $expenseLedger)
        );
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_expense_ledger(): void
    {
        $expenseLedger = ExpenseLedger::factory()->create();

        $response = $this->get(route('expense-ledgers.show', $expenseLedger));

        $response
            ->assertOk()
            ->assertViewIs('app.expense_ledgers.show')
            ->assertViewHas('expenseLedger');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_expense_ledger(): void
    {
        $expenseLedger = ExpenseLedger::factory()->create();

        $response = $this->get(route('expense-ledgers.edit', $expenseLedger));

        $response
            ->assertOk()
            ->assertViewIs('app.expense_ledgers.edit')
            ->assertViewHas('expenseLedger');
    }

    /**
     * @test
     */
    public function it_updates_the_expense_ledger(): void
    {
        $expenseLedger = ExpenseLedger::factory()->create();

        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(15),
            'invoice_number' => $this->faker->text(255),
            'purchase_price' => $this->faker->randomNumber(2),
            'seller' => $this->faker->text(),
            'purchase_date' => $this->faker->date(),
            'payment_method' => $this->faker->text(255),
        ];

        $response = $this->put(
            route('expense-ledgers.update', $expenseLedger),
            $data
        );

        $data['id'] = $expenseLedger->id;

        $this->assertDatabaseHas('expense_ledgers', $data);

        $response->assertRedirect(
            route('expense-ledgers.edit', $expenseLedger)
        );
    }

    /**
     * @test
     */
    public function it_deletes_the_expense_ledger(): void
    {
        $expenseLedger = ExpenseLedger::factory()->create();

        $response = $this->delete(
            route('expense-ledgers.destroy', $expenseLedger)
        );

        $response->assertRedirect(route('expense-ledgers.index'));

        $this->assertModelMissing($expenseLedger);
    }
}
