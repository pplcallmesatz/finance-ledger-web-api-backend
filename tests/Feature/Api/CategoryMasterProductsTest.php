<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Product;
use App\Models\CategoryMaster;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryMasterProductsTest extends TestCase
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
    public function it_gets_category_master_products(): void
    {
        $categoryMaster = CategoryMaster::factory()->create();
        $products = Product::factory()
            ->count(2)
            ->create([
                'category_master_id' => $categoryMaster->id,
            ]);

        $response = $this->getJson(
            route('api.category-masters.products.index', $categoryMaster)
        );

        $response->assertOk()->assertSee($products[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_category_master_products(): void
    {
        $categoryMaster = CategoryMaster::factory()->create();
        $data = Product::factory()
            ->make([
                'category_master_id' => $categoryMaster->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.category-masters.products.store', $categoryMaster),
            $data
        );

        $this->assertDatabaseHas('products', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $product = Product::latest('id')->first();

        $this->assertEquals($categoryMaster->id, $product->category_master_id);
    }
}
