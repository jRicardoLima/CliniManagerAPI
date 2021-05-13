<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyProfileProfileAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles_authorizations', function (Blueprint $table) {
            $table->foreignId('profile_id')->constrained('profiles');
            $table->foreignId('authorization_id')->constrained('authorizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles_authorizations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profile_id');
            $table->dropConstrainedForeignId('authorization_id');
        });
    }
}
