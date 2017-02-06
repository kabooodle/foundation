<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pageviews', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('shoppable_type');
            $table->integer('shoppable_id')->unsigned();
            $table->bigInteger('inventory_id')->unsigned();
            $table->string('ip_address');
            $table->timestamps();

            $table->foreign('inventory_id')
                ->references('id')->on('inventory')
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
        Schema::drop('pageviews');
    }
}
