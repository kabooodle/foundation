<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlashsalesGroupsAndPivotTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flashsales_groups', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('name');
            $table->bigInteger('owner_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('owner_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create('flashsales_groups_users', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('flashsales_group_id')->unsigned();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('flashsales_group_id')
                ->references('id')->on('flashsales_groups')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        DB::update("ALTER TABLE flashsales_groups AUTO_INCREMENT = 9909101;");
        DB::update("ALTER TABLE flashsales_groups_users AUTO_INCREMENT = 9909101;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flashsales_groups');
        Schema::dropIfExists('flashsales_groups_users');
    }
}
