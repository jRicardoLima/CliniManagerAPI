<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('contry')->nullable();
            $table->string('state')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('city');
            $table->string('neighborhood');
            $table->string('street');
            $table->string('number')->nullable();
            $table->string('telphone')->nullable();
            $table->string('celphone')->nullable();
            $table->string('email')->nullable();
            $table->string('observation')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('adresses');
    }
}
