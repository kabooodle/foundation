<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookNodesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_nodes', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->enum('facebook_node_type', ['group','album', 'photo', 'comment']);
            $table->bigInteger('facebook_node_id')->unsigned();
            $table->bigInteger('facebook_post_id')->unsigned();
            $table->text('facebook_node_name');
            $table->text('facebook_data')->nullable();
            $table->bigInteger('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('facebook_nodes', function(Blueprint $table){
           $table->foreign('updated_by', 'facebook_nodes_fk1')
               ->references('id')->on('users')
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
