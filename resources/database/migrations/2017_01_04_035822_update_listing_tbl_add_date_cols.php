<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateListingTblAddDateCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listings', function(Blueprint $table){
            $table->timestamp('scheduled_until')->nullable()->after('scheduled_for');
            $table->timestamp('claimable_until')->nullable()->after('claimable_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listings', function(Blueprint $table){
           $table->dropColumn(['scheduled_until', 'claimable_until']);
        });
    }
}
