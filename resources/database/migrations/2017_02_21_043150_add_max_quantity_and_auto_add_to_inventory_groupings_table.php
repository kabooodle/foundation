<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxQuantityAndAutoAddToInventoryGroupingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_groupings', function (Blueprint $table) {
            $table->boolean('auto_add')->default(true)->after('cover_photo_file_id');
            $table->boolean('max_quantity')->default(true)->after('auto_add');
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
            $table->dropColumn('auto_add');
            $table->dropColumn('max_quantity');
        });
    }
}
