<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_ledgers', function (Blueprint $table) {
            //
            $table->string('total_product_price')->nullable()->change();
            $table->string('selling_product_price')->nullable()->change();
            $table->string('total_customer_price')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_ledgers', function (Blueprint $table) {
            //
            $table->string('total_product_price')->nullable(false)->change();
            $table->string('selling_product_price')->nullable(false)->change();
            $table->string('total_customer_price')->nullable(false)->change();

        });
    }
};
