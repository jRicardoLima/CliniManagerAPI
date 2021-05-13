<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->foreignId('bussiness_unit_id')->constrained('bussiness_units');
            $table->foreignId('suppliers_id')->constrained('suppliers');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('organization_id')->constrained('organizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bussiness_unit_id');
            $table->dropConstrainedForeignId('suppliers_id');
            $table->dropConstrainedForeignId('product_id');
            $table->dropConstrainedForeignId('organization_id');
        });
    }
}
