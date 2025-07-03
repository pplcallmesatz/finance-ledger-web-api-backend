<?php

namespace Database\Seeders;

use App\Models\CategoryMaster;
use Illuminate\Database\Seeder;

class CategoryMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryMaster::factory()
            ->count(5)
            ->create();
    }
}
