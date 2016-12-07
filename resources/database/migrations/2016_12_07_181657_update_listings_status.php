<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateListingsStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE listings CHANGE status status  ENUM('scheduled','queued','processing','partial','success','completed','deleted','queued_delete','ignored_duplicate')");
        DB::statement("ALTER TABLE listing_items change status status  ENUM('scheduled','queued','processing', 'partial','success','completed','deleted','queued_delete','ignored_duplicate')");
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
