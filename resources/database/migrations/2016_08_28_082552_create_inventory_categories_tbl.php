<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryCategoriesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_categories', function(Blueprint $table){
            $table->increments('id');
            $table->integer('inventory_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table(\Kabooodle\Models\FlashsaleItems::getTableName(), function (Blueprint $table) {
            $table->foreign('inventory_id')
                ->references('id')->on('inventory')
                ->onDelete('cascade')
                ->onUpdated('cascade');

            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->onDelete('cascade')
                ->onUpdated('cascade');
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
