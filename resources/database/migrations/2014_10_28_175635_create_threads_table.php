<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messenger_threads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject')->nullable();
            $table->timestamps();
        });


        DB::update("ALTER TABLE messenger_threads AUTO_INCREMENT = 9919901;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('messenger_threads');
    }
}
