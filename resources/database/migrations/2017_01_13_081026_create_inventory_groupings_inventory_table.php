<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryGroupingsInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_groupings_inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('inventory_grouping_id')->unsigned();
            $table->bigInteger('inventory_id')->unsigned();
        });

        Schema::table('inventory_groupings_inventory', function(Blueprint $table){
            $table->foreign('inventory_grouping_id')
                ->references('id')->on('inventory_groupings')
                ->onDelete('cascade')
                ->onUpdate('cascade');

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
        Schema::drop('inventory_groupings_inventory');
    }
}
