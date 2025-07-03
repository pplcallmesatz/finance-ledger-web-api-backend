<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\SalesLedger;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserSalesLedgersTest extends TestCase
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
    public function it_gets_user_sales_ledgers(): void
    {
        $user = User::factory()->create();
        $salesLedgers = SalesLedger::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->getJson(
            route('api.users.sales-ledgers.index', $user)
        );

        $response->assertOk()->assertSee($salesLedgers[0]->payment_status);
    }

    /**
     * @test
     */
    public function it_stores_the_user_sales_ledgers(): void
    {
        $user = User::factory()->create();
        $data = SalesLedger::factory()
            ->make([
                'user_id' => $user->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.users.sales-ledgers.store', $user),
            $data
        );

        $this->assertDatabaseHas('sales_ledgers', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $salesLedger = SalesLedger::latest('id')->first();

        $this->assertEquals($user->id, $salesLedger->user_id);
    }
}
