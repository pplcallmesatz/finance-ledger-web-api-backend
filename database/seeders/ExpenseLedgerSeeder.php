<?php

namespace Database\Seeders;

use App\Models\ExpenseLedger;
use Illuminate\Database\Seeder;

class ExpenseLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseLedger::factory()
            ->count(5)
            ->create();
    }
}
