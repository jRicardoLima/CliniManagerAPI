<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumsPinMovementStockAndDateMovementStockMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->date('date_movement')->after('total_amount');
            $table->longText('pin')->unique()
                                           ->nullable()
                                           ->after('date_movement');
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
            $table->dropColumn('date_movement');
            $table->dropColumn('pin');
        });
    }
}
