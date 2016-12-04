<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlashsalesPivotSellersTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flashsales_sellers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('flashsales_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('flashsales_sellers', function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('flashsales_id')
                ->references('id')->on('flashsales')
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
