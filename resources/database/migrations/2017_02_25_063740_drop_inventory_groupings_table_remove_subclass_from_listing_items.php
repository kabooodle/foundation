<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropInventoryGroupingsTableRemoveSubclassFromListingItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('inventory_groupings_inventory', function (Blueprint $table) {
            $table->dropForeign('inventory_groupings_inventory_inventory_grouping_id_foreign');
            $table->dropForeign('inventory_groupings_inventory_inventory_id_foreign');
        });

        Schema::table('inventory_groupings_inventory', function(Blueprint $table){
            $table->foreign('inventory_grouping_id')
                ->references('id')->on('listables')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('inventory_id')
                ->references('id')->on('listables')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::table('inventory_groupings', function (Blueprint $table) {
            $table->drop();
        });

        DB::table('inventory_groupings_inventory')->truncate();

        Schema::table('listing_items', function (Blueprint $table) {
            $table->dropColumn('subclass_name');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_groupings', function (Blueprint $table) {
            //
        });
    }
}
