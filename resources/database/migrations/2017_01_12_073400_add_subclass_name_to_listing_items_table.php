<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubclassNameToListingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listing_items', function (Blueprint $table) {
            $table->string('subclass_name')->after('flashsale_id');
        });

        DB::statement('UPDATE `listing_items` SET `subclass_name` = "Kabooodle\\Models\\ListingItemSingle"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listing_items', function (Blueprint $table) {
            $table->dropColumn('subclass_name');
        });
    }
}
