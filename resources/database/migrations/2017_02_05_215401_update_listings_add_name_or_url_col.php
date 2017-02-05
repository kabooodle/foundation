<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateListingsAddNameOrUrlCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listings', function(Blueprint $table){
            $table->string('name')->nullable()->after('uuid');
        });

        DB::statement("ALTER TABLE listings CHANGE type type  ENUM('facebook','flashsale','custom')");
        DB::statement("ALTER TABLE listing_items change type type  ENUM('facebook','flashsale','custom')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listings', function(Blueprint $table){
            $table->dropColumn('name');
        });

        DB::statement("ALTER TABLE listings CHANGE type type  ENUM('facebook','flashsale')");
        DB::statement("ALTER TABLE listing_items change type type  ENUM('facebook','flashsale')");
    }
}
