<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacebookColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('facebook_user_id')->unsigned()->index()->nullable();
            $table->string('facebook_access_token')->nullable();
            $table->timestamp('facebook_access_token_expires')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table)
        {
            $table->dropColumn(
                'facebook_user_id',
                'facebook_access_token',
                'facebook_access_token_expires'
            );
        });
    }
}
