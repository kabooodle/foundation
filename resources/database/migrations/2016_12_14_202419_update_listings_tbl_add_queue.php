<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateListingsTblAddQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listings', function(Blueprint $table){
            $table->bigInteger('queue_id')->unsigned()->nullable()->after('flashsale_id');
        });

        Schema::table('listing_items', function(Blueprint $table){
            $table->bigInteger('queue_id')->unsigned()->nullable()->after('inventory_id');
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
