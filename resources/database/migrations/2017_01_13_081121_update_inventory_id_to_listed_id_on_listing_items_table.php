<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInventoryIdToListedIdOnListingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listing_items', function(Blueprint $table) {
            $table->dropForeign('listing_items_inventory_id_foreign');
        });

        DB::statement('ALTER TABLE `listing_items` CHANGE COLUMN `inventory_id` `listable_id` BIGINT UNSIGNED NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `listing_items` CHANGE COLUMN `listable_id` `inventory_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('listing_items', function(Blueprint $table) {
            $table->foreign('inventory_id')
                ->references('id')->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
}
