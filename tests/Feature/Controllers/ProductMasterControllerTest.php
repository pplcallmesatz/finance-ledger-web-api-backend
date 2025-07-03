<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\ProductMaster;

use App\Models\CategoryMaster;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductMasterControllerTest extends TestCase
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
    public function it_displays_index_view_with_product_masters(): void
    {
        $productMasters = ProductMaster::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('product-masters.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.product_masters.index')
            ->assertViewHas('productMasters');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_product_master(): void
    {
        $response = $this->get(route('product-masters.create'));

        $response->assertOk()->assertViewIs('app.product_masters.create');
    }

    /**
     * @test
     */
    public function it_stores_the_product_master(): void
    {
        $data = ProductMaster::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('product-masters.store'), $data);

        unset($data['batch_number']);

        $this->assertDatabaseHas('product_masters', $data);

        $productMaster = ProductMaster::latest('id')->first();

        $response->assertRedirect(
            route('product-masters.edit', $productMaster)
        );
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_product_master(): void
    {
        $productMaster = ProductMaster::factory()->create();

        $response = $this->get(route('product-masters.show', $productMaster));

        $response
            ->assertOk()
            ->assertViewIs('app.product_masters.show')
            ->assertViewHas('productMaster');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_product_master(): void
    {
        $productMaster = ProductMaster::factory()->create();

        $response = $this->get(route('product-masters.edit', $productMaster));

        $response
            ->assertOk()
            ->assertViewIs('app.product_masters.edit')
            ->assertViewHas('productMaster');
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

        $response = $this->put(
            route('product-masters.update', $productMaster),
            $data
        );

        unset($data['batch_number']);

        $data['id'] = $productMaster->id;

        $this->assertDatabaseHas('product_masters', $data);

        $response->assertRedirect(
            route('product-masters.edit', $productMaster)
        );
    }

    /**
     * @test
     */
    public function it_deletes_the_product_master(): void
    {
        $productMaster = ProductMaster::factory()->create();

        $response = $this->delete(
            route('product-masters.destroy', $productMaster)
        );

        $response->assertRedirect(route('product-masters.index'));

        $this->assertModelMissing($productMaster);
    }
}
