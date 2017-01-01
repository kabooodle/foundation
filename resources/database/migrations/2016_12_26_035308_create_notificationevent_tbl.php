<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationeventTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_user_notices', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->integer('notification_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('reference_id')->unsigned()->nullable();
            $table->text('reference_type')->nullable();
            $table->text('reference_url')->nullable();
            $table->string('name');
            $table->text('description');
            $table->text('payload')->nullable();
            $table->tinyInteger('is_read')->default(0);
            $table->timestamp('read_at')->nullable();
            $table->enum('priority', ['high', 'med', 'low'])->default('low');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('notification_id')
                ->references('id')->on('notifications')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        DB::update("ALTER TABLE notification_user_notices AUTO_INCREMENT = 9919121;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notification_user_notices');
    }
}
