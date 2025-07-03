<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\ProductMaster;

use App\Models\CategoryMaster;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductMasterTest extends TestCase
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
    public function it_gets_product_masters_list(): void
    {
        $productMasters = ProductMaster::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.product-masters.index'));

        $response->assertOk()->assertSee($productMasters[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_product_master(): void
    {
        $data = ProductMaster::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.product-masters.store'), $data);

        unset($data['batch_number']);

        $this->assertDatabaseHas('product_masters', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_product_master(): void
    {
        $productMaster = ProductMaster::factory()->create();

        $categoryMaster = CategoryMaster::factory()->create();

        $data = [
            'name' => $this->faker->name(),
            'purchase_price' => $this->faker->randomNumber(2),
            'purchase_date' => $this->faker->date(),
            'manufacturing_date' => $this->faker->date(),
            'transportation_cost' => $this->faker->randomNumber(2),
            'invoice_number' => $this->faker->text(255),
            'vendor' => $this->faker->text(),
            'quantity_purchased' => $this->faker->randomNumber(0),
            'batch_number' => $this->faker->text(255),
            'category_id' => $categoryMaster->id,
        ];

        $response = $this->putJson(
            route('api.product-masters.update', $productMaster),
            $data
        );

        unset($data['batch_number']);

        $data['id'] = $productMaster->id;

        $this->assertDatabaseHas('product_masters', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_product_master(): void
    {
        $productMaster = ProductMaster::factory()->create();

        $response = $this->deleteJson(
            route('api.product-masters.destroy', $productMaster)
        );

        $this->assertModelMissing($productMaster);

        $response->assertNoContent();
    }
}
