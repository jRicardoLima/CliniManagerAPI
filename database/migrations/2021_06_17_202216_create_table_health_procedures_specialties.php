<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableHealthProceduresSpecialties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_procedure_specialties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_procedure_id')->constrained('health_procedures');
            $table->foreignId('specialtie_id')->constrained('specialties');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_procedure_specialties');
    }
}
