<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookItemsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_items', function(Blueprint $table){
            $table->bigInteger('id')->unsigned();
            $table->bigInteger('inventory_id')->unsigned();
            $table->bigInteger('seller_id')->unsigned();
            $table->bigInteger('facebook_node_id')->unsigned();
            $table->bigInteger('facebook_post_id')->unsigned();
            $table->timestamp('facebook_posted_at');
            $table->text('facebook_parameters')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table(\Kabooodle\Models\Claims::getTableName(), function(Blueprint $table){
            $table->foreign('inventory_id')
                ->references('id')->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
