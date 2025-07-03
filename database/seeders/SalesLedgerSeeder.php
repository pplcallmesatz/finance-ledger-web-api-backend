<?php

namespace Database\Seeders;

use App\Models\SalesLedger;
use Illuminate\Database\Seeder;

class SalesLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalesLedger::factory()
            ->count(5)
            ->create();
    }
}
