<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateListingsTblAddFacebookResponseTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listing_items', function(Blueprint $table){
            $table->bigInteger('fb_response_object_id')->unsigned()->after('fb_album_node_id')->nullable();
            $table->longText('fb_response')->after('uuid')->nullable();
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
            $table->dropColumn(['fb_response', 'fb_response_object_id']);
        });
    }
}
