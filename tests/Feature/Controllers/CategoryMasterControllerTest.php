<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\CategoryMaster;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryMasterControllerTest extends TestCase
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
    public function it_displays_index_view_with_category_masters(): void
    {
        $categoryMasters = CategoryMaster::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('category-masters.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.category_masters.index')
            ->assertViewHas('categoryMasters');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_category_master(): void
    {
        $response = $this->get(route('category-masters.create'));

        $response->assertOk()->assertViewIs('app.category_masters.create');
    }

    /**
     * @test
     */
    public function it_stores_the_category_master(): void
    {
        $data = CategoryMaster::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('category-masters.store'), $data);

        $this->assertDatabaseHas('category_masters', $data);

        $categoryMaster = CategoryMaster::latest('id')->first();

        $response->assertRedirect(
            route('category-masters.edit', $categoryMaster)
        );
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_category_master(): void
    {
        $categoryMaster = CategoryMaster::factory()->create();

        $response = $this->get(route('category-masters.show', $categoryMaster));

        $response
            ->assertOk()
            ->assertViewIs('app.category_masters.show')
            ->assertViewHas('categoryMaster');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_category_master(): void
    {
        $categoryMaster = CategoryMaster::factory()->create();

        $response = $this->get(route('category-masters.edit', $categoryMaster));

        $response
            ->assertOk()
            ->assertViewIs('app.category_masters.edit')
            ->assertViewHas('categoryMaster');
    }

    /**
     * @test
     */
    public function it_updates_the_category_master(): void
    {
        $categoryMaster = CategoryMaster::factory()->create();

        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(15),
            'symbol' => $this->faker->text(255),
        ];

        $response = $this->put(
            route('category-masters.update', $categoryMaster),
            $data
        );

        $data['id'] = $categoryMaster->id;

        $this->assertDatabaseHas('category_masters', $data);

        $response->assertRedirect(
            route('category-masters.edit', $categoryMaster)
        );
    }

    /**
     * @test
     */
    public function it_deletes_the_category_master(): void
    {
        $categoryMaster = CategoryMaster::factory()->create();

        $response = $this->delete(
            route('category-masters.destroy', $categoryMaster)
        );

        $response->assertRedirect(route('category-masters.index'));

        $this->assertModelMissing($categoryMaster);
    }
}
