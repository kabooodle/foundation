<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messenger_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('thread_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->text('body');
            $table->timestamps();


            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('thread_id')
                ->references('id')->on('messenger_threads')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        DB::update("ALTER TABLE messenger_messages AUTO_INCREMENT = 9919900;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('messenger_messages');
    }
}
