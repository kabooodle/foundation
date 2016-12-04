<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitationsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('uuid');
            $table->integer('invitable_id')->unsigned();
            $table->string('invitable_type');
            $table->timestamp('invited_at');
            $table->bigInteger('invited_by')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('email')->nullable();
            $table->tinyInteger('accepted')->default(0);
            $table->timestamp('accepted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('invitations', function(Blueprint $table){
            $table->foreign('invited_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
