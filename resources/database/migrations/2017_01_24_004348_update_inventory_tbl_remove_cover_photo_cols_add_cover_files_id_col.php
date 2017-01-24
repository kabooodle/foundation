<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInventoryTblRemoveCoverPhotoColsAddCoverFilesIdCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function(Blueprint $table){
            $table->dropColumn('cover_photo_file_key');
        });

        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('avatar');
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
            $table->text('cover_photo_file_key')->after('initial_qty');
        });

        Schema::table('users', function(Blueprint $table){
            $table->string('avatar')->nullable()->after('password');
        });
    }
}
