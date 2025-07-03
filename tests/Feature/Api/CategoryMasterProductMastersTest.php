<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\ProductMaster;
use App\Models\CategoryMaster;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryMasterProductMastersTest extends TestCase
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
    public function it_gets_category_master_product_masters(): void
    {
        $categoryMaster = CategoryMaster::factory()->create();
        $productMasters = ProductMaster::factory()
            ->count(2)
            ->create([
                'category_id' => $categoryMaster->id,
            ]);

        $response = $this->getJson(
            route('api.category-masters.product-masters.index', $categoryMaster)
        );

        $response->assertOk()->assertSee($productMasters[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_category_master_product_masters(): void
    {
        $categoryMaster = CategoryMaster::factory()->create();
        $data = ProductMaster::factory()
            ->make([
                'category_id' => $categoryMaster->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route(
                'api.category-masters.product-masters.store',
                $categoryMaster
            ),
            $data
        );

        unset($data['batch_number']);

        $this->assertDatabaseHas('product_masters', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $productMaster = ProductMaster::latest('id')->first();

        $this->assertEquals($categoryMaster->id, $productMaster->category_id);
    }
}
