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
        Schema::table('inventory', function(Blueprint $table){
            $table->decimal('wholesale_price_usd', 6,2)->after('price_usd');
        });

        Schema::table(\Kabooodle\Models\InventoryTypeStyles::getTableName(), function(Blueprint $table){
            $table->decimal('wholesale_price_usd', 6,2)->after('sort_order');
            $table->decimal('wholesale_price_usd_less_5_percent', 6,2)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory', function(Blueprint $table){
            $table->dropColumn('wholesale_price_usd');
        });

        Schema::table(\Kabooodle\Models\InventoryTypeStyles::getTableName(), function(Blueprint $table){
            $table->dropColumn(['wholesale_price_usd', 'wholesale_price_usd_less_5_percent']);
        });
    }
}
