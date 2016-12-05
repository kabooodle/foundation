<?php

use Kabooodle\Models\FacebookNodes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacebookNodesUsersTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_nodes_users', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('db_node_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('facebook_node_id')->unsigned();
            $table->enum('node_type', [FacebookNodes::NODE_ALBUM, FacebookNodes::NODE_GROUP, FacebookNodes::NODE_COMMENT, FacebookNodes::NODE_PHOTO]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('facebook_nodes_users');
    }
}
