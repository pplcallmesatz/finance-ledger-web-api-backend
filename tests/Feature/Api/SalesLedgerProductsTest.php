<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Product;
use App\Models\SalesLedger;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesLedgerProductsTest extends TestCase
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
    public function it_gets_sales_ledger_products(): void
    {
        $salesLedger = SalesLedger::factory()->create();
        $product = Product::factory()->create();

        $salesLedger->products()->attach($product);

        $response = $this->getJson(
            route('api.sales-ledgers.products.index', $salesLedger)
        );

        $response->assertOk()->assertSee($product->name);
    }

    /**
     * @test
     */
    public function it_can_attach_products_to_sales_ledger(): void
    {
        $salesLedger = SalesLedger::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson(
            route('api.sales-ledgers.products.store', [$salesLedger, $product])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $salesLedger
                ->products()
                ->where('products.id', $product->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_products_from_sales_ledger(): void
    {
        $salesLedger = SalesLedger::factory()->create();
        $product = Product::factory()->create();

        $response = $this->deleteJson(
            route('api.sales-ledgers.products.store', [$salesLedger, $product])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $salesLedger
                ->products()
                ->where('products.id', $product->id)
                ->exists()
        );
    }
}
