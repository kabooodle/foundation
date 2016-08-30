<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFlashsalesTblAddHostId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\Kabooodle\Models\FlashSales::getTableName(), function(Blueprint $table){
            $table->dropForeign('flashsales_ibfk_1');
            $table->dropColumn('group_id');
            $table->integer('host_id')->unsigned()->before('active');
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
