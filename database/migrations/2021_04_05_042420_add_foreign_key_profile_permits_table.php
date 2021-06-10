<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyProfilePermitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles_permits', function (Blueprint $table) {
            $table->foreignId('profile_id')->constrained('profiles');
            $table->foreignId('permission_id')->constrained('permits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles_permits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profile_id');
            $table->dropConstrainedForeignId('permission_id');
        });
    }
}
