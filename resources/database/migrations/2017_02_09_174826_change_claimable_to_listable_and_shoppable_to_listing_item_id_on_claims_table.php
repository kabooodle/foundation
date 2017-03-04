<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeClaimableToListableAndShoppableToListingItemIdOnClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `claims` CHANGE COLUMN `claimable_id` `listable_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `claims` CHANGE COLUMN `claimable_type` `listable_type` VARCHAR(255)');
        DB::statement('ALTER TABLE `claims` CHANGE COLUMN `shoppable_id` `listing_item_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('shoppable_type');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->foreign('listing_item_id')
                ->references('id')->on('listing_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->string('shoppable_type')->after('shoppable_id');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign('claims_listing_item_id_foreign');
        });

        DB::statement('ALTER TABLE `claims` CHANGE COLUMN `listable_id` `claimable_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `claims` CHANGE COLUMN `listable_type` `claimable_type` VARCHAR(255)');
        DB::statement('ALTER TABLE `claims` CHANGE COLUMN `listing_item_id` `shoppable_id` BIGINT UNSIGNED NOT NULL');

        DB::statement('UPDATE claims SET shoppable_type = "Kabooodle\\Models\\ListingItems"');
    }
}
