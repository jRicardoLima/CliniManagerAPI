<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyBussinessUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bussiness_units', function (Blueprint $table) {
            $table->foreignId('address_id')->constrained('adresses');
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
        Schema::table('bussiness_units', function (Blueprint $table) {
            $table->dropConstrainedForeignId('address_id');
            $table->dropConstrainedForeignId('organization_id');
        });
    }
}
