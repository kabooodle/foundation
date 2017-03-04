<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateListingAndListingItemsTblUpdateStatusCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE listings CHANGE status status  ENUM('scheduled','scheduled_delete', 'queued','processing','processing_delete','partial','success','completed','deleted','queued_delete','ignored_duplicate','delete_failed','throttled','failed')");
        DB::statement("ALTER TABLE listing_items change status status  ENUM('scheduled','scheduled_delete','queued','processing','processing_delete','partial','success','completed','deleted','queued_delete','ignored_duplicate','delete_failed','throttled','failed')");
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
