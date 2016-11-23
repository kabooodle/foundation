<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInventoryAddWholesalePrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\Kabooodle\Models\Inventory::getTableName(), function(Blueprint $table){
            $table->decimal('wholesale_price_usd', 6,2)->after('price_usd');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(\Kabooodle\Models\Inventory::getTableName(), function(Blueprint $table){
            $table->dropColumn('wholesale_price_usd');
        });
    }
}
