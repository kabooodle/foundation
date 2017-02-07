<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCoverPhotoFileKeyFromInventoryGroupingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_groupings', function (Blueprint $table) {
            $table->dropColumn('cover_photo_file_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_groupings', function (Blueprint $table) {
            $table->text('cover_photo_file_key')->nullable()->after('initial_qty');
        });
    }
}
