<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeInventoryItemObjectDataToListableOnClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `claims` CHANGE COLUMN `inventory_item_object_data` `listable_item_object_data` LONGBLOB NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `claims` CHANGE COLUMN `listable_item_object_data` `inventory_item_object_data` LONGBLOB NOT NULL');
    }
}
