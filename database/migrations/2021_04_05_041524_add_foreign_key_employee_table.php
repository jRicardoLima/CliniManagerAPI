<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->foreignId('occupation_id')->constrained('occupations');
            $table->foreignId('address_id')->constrained('adresses');
            $table->foreignId('bussiness_id')->constrained('bussiness_units');
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
        Schema::table('employee', function (Blueprint $table) {
            $table->dropConstrainedForeignId('occupation_id');
            $table->dropConstrainedForeignId('address_id');
            $table->dropConstrainedForeignId('bussiness_id');
            $table->dropConstrainedForeignId('organization_id');

        });
    }
}
