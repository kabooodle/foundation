<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClaimsAddShippedManuallyColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\Kabooodle\Models\Claims::getTableName(), function(Blueprint $table){
            $table->tinyInteger('shipped_manually')->default(0)->after('rejected_reason');
            $table->timestamp('shipped_manually_on')->nullable()->default(null)->after('rejected_reason');
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
