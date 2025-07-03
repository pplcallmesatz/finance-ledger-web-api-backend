<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\SalesLedger;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesLedgerTest extends TestCase
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
    public function it_gets_sales_ledgers_list(): void
    {
        $salesLedgers = SalesLedger::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.sales-ledgers.index'));

        $response->assertOk()->assertSee($salesLedgers[0]->payment_status);
    }

    /**
     * @test
     */
    public function it_stores_the_sales_ledger(): void
    {
        $data = SalesLedger::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.sales-ledgers.store'), $data);

        $this->assertDatabaseHas('sales_ledgers', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_sales_ledger(): void
    {
        $salesLedger = SalesLedger::factory()->create();

        $user = User::factory()->create();

        $data = [
            'user_id' => $this->faker->randomNumber(),
            'total_product_price' => $this->faker->randomNumber(2),
            'selling_product_price' => $this->faker->randomNumber(2),
            'payment_status' => $this->faker->text(255),
            'remarks' => $this->faker->text(),
            'company_address' => $this->faker->text(),
            'invoice_number' => $this->faker->text(255),
            'payment_method' => $this->faker->text(255),
            'user_id' => $user->id,
        ];

        $response = $this->putJson(
            route('api.sales-ledgers.update', $salesLedger),
            $data
        );

        $data['id'] = $salesLedger->id;

        $this->assertDatabaseHas('sales_ledgers', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_sales_ledger(): void
    {
        $salesLedger = SalesLedger::factory()->create();

        $response = $this->deleteJson(
            route('api.sales-ledgers.destroy', $salesLedger)
        );

        $this->assertModelMissing($salesLedger);

        $response->assertNoContent();
    }
}
