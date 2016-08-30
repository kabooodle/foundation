<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupInvitations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups_invitations', function(Blueprint $table){
            $table->increments('id');
            $table->binary('uuid');
            $table->integer('invited_by')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->string('email_address', 255);
            $table->timestamp('invited_at')->nullable();
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
        //
    }
}
