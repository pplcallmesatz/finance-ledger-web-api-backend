<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\CategoryMaster;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryMasterTest extends TestCase
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
    public function it_gets_category_masters_list(): void
    {
        $categoryMasters = CategoryMaster::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.category-masters.index'));

        $response->assertOk()->assertSee($categoryMasters[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_category_master(): void
    {
        $data = CategoryMaster::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.category-masters.store'), $data);

        $this->assertDatabaseHas('category_masters', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
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

        $response = $this->putJson(
            route('api.category-masters.update', $categoryMaster),
            $data
        );

        $data['id'] = $categoryMaster->id;

        $this->assertDatabaseHas('category_masters', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_category_master(): void
    {
        $categoryMaster = CategoryMaster::factory()->create();

        $response = $this->deleteJson(
            route('api.category-masters.destroy', $categoryMaster)
        );

        $this->assertModelMissing($categoryMaster);

        $response->assertNoContent();
    }
}
