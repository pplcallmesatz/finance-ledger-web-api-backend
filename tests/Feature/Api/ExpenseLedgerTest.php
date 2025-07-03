<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\ExpenseLedger;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseLedgerTest extends TestCase
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
    public function it_gets_expense_ledgers_list(): void
    {
        $expenseLedgers = ExpenseLedger::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.expense-ledgers.index'));

        $response->assertOk()->assertSee($expenseLedgers[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_expense_ledger(): void
    {
        $data = ExpenseLedger::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.expense-ledgers.store'), $data);

        $this->assertDatabaseHas('expense_ledgers', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
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

        $response = $this->putJson(
            route('api.expense-ledgers.update', $expenseLedger),
            $data
        );

        $data['id'] = $expenseLedger->id;

        $this->assertDatabaseHas('expense_ledgers', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_expense_ledger(): void
    {
        $expenseLedger = ExpenseLedger::factory()->create();

        $response = $this->deleteJson(
            route('api.expense-ledgers.destroy', $expenseLedger)
        );

        $this->assertModelMissing($expenseLedger);

        $response->assertNoContent();
    }
}
