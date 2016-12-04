<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRevisionsTblAddFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `revisions` MODIFY `user` BIGINT UNSIGNED NOT NULL;');

        Schema::table('revisions', function(Blueprint $table){
            $table->foreign('user')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdated('cascade');
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
