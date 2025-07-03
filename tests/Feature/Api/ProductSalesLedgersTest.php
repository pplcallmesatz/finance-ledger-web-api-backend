<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Product;
use App\Models\SalesLedger;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductSalesLedgersTest extends TestCase
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
    public function it_gets_product_sales_ledgers(): void
    {
        $product = Product::factory()->create();
        $salesLedger = SalesLedger::factory()->create();

        $product->salesLedgers()->attach($salesLedger);

        $response = $this->getJson(
            route('api.products.sales-ledgers.index', $product)
        );

        $response->assertOk()->assertSee($salesLedger->payment_status);
    }

    /**
     * @test
     */
    public function it_can_attach_sales_ledgers_to_product(): void
    {
        $product = Product::factory()->create();
        $salesLedger = SalesLedger::factory()->create();

        $response = $this->postJson(
            route('api.products.sales-ledgers.store', [$product, $salesLedger])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $product
                ->salesLedgers()
                ->where('sales_ledgers.id', $salesLedger->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_sales_ledgers_from_product(): void
    {
        $product = Product::factory()->create();
        $salesLedger = SalesLedger::factory()->create();

        $response = $this->deleteJson(
            route('api.products.sales-ledgers.store', [$product, $salesLedger])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $product
                ->salesLedgers()
                ->where('sales_ledgers.id', $salesLedger->id)
                ->exists()
        );
    }
}
