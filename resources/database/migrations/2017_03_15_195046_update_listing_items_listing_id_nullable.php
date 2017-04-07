<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateListingItemsListingIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listing_items', function (Blueprint $table) {
            DB::statement('ALTER TABLE listing_items MODIFY listing_id BIGINT UNSIGNED NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listing_items', function (Blueprint $table) {
            DB::statement('ALTER TABLE listing_items MODIFY listing_id BIGINT UNSIGNED NOT NULL;');
        });
    }
}
