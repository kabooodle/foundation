<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\Claims::getTableName(), function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('inventory_id')->unsigned();
            $table->bigInteger('claimed_by')->unsigned();
            $table->integer('shoppable_id')->unsigned();
            $table->string('shoppable_type');
            $table->text('inventory_item_object_data');
            $table->integer('price');
            $table->boolean('accepted')->nullable()->default(null);
            $table->dateTime('accepted_on')->nullable()->default(null);
            $table->dateTime('rejected_on')->nullable()->default(null);
            $table->bigInteger('rejected_by')->nullable()->default(null)->unsigned();
            $table->text('rejected_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table(\Kabooodle\Models\Claims::getTableName(), function(Blueprint $table){
            $table->foreign('claimed_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('rejected_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        DB::update("ALTER TABLE ".\Kabooodle\Models\Claims::getTableName()." AUTO_INCREMENT = 9893199;");
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
