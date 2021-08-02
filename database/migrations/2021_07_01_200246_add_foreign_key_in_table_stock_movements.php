<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInTableStockMovements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained();
            $table->foreignId('bussiness_unit_id')->constrained();
            $table->foreignId('supplier_id')->constrained()->nullOnDelete();
            $table->foreignId('organization_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropConstrainedForeignId('bussiness_unit_id');
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropConstrainedForeignId('organization_id');
        });
    }
}
