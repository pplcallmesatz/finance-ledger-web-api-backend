<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\SalesLedger;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesLedgerControllerTest extends TestCase
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
    public function it_displays_index_view_with_sales_ledgers(): void
    {
        $salesLedgers = SalesLedger::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('sales-ledgers.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.sales_ledgers.index')
            ->assertViewHas('salesLedgers');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_sales_ledger(): void
    {
        $response = $this->get(route('sales-ledgers.create'));

        $response->assertOk()->assertViewIs('app.sales_ledgers.create');
    }

    /**
     * @test
     */
    public function it_stores_the_sales_ledger(): void
    {
        $data = SalesLedger::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('sales-ledgers.store'), $data);

        $this->assertDatabaseHas('sales_ledgers', $data);

        $salesLedger = SalesLedger::latest('id')->first();

        $response->assertRedirect(route('sales-ledgers.edit', $salesLedger));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_sales_ledger(): void
    {
        $salesLedger = SalesLedger::factory()->create();

        $response = $this->get(route('sales-ledgers.show', $salesLedger));

        $response
            ->assertOk()
            ->assertViewIs('app.sales_ledgers.show')
            ->assertViewHas('salesLedger');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_sales_ledger(): void
    {
        $salesLedger = SalesLedger::factory()->create();

        $response = $this->get(route('sales-ledgers.edit', $salesLedger));

        $response
            ->assertOk()
            ->assertViewIs('app.sales_ledgers.edit')
            ->assertViewHas('salesLedger');
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

        $response = $this->put(
            route('sales-ledgers.update', $salesLedger),
            $data
        );

        $data['id'] = $salesLedger->id;

        $this->assertDatabaseHas('sales_ledgers', $data);

        $response->assertRedirect(route('sales-ledgers.edit', $salesLedger));
    }

    /**
     * @test
     */
    public function it_deletes_the_sales_ledger(): void
    {
        $salesLedger = SalesLedger::factory()->create();

        $response = $this->delete(route('sales-ledgers.destroy', $salesLedger));

        $response->assertRedirect(route('sales-ledgers.index'));

        $this->assertModelMissing($salesLedger);
    }
}
