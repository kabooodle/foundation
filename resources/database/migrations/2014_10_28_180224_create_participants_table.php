<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messenger_participants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('thread_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamp('last_read')->nullable();
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

        DB::update("ALTER TABLE messenger_participants AUTO_INCREMENT = 99199452;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('messenger_participants');
    }
}
