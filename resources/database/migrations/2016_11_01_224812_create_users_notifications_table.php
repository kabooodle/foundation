<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_notificationsettings', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('notification_id')->unsigned();
            $table->integer('notificationable_id')->nullable();
            $table->text('notificationable_type')->nullable();
            $table->tinyInteger('email')->default(1);
            $table->tinyInteger('web')->default(1);

            $table->foreign('notification_id')
                ->references('id')->on('notifications')
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
