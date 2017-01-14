<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInventoryTblAddCoverPhotoData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function(Blueprint $table){
            $table->bigInteger('cover_photo_file_id')->after('initial_qty')->nullable();
            $table->text('cover_photo_file_key')->after('initial_qty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory', function(Blueprint $table){
            $table->dropColumn(['cover_photo_file_id', 'cover_photo_file_key']);
        });
    }
}
