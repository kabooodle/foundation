<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateListingItemsAddScheduledForCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listing_items', function(Blueprint $table){
            $table->timestamp('scheduled_for_deletion')->after('fb_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listing_items', function(Blueprint $table){
            $table->dropColumn(['scheduled_for_deletion']);
        });
    }
}
