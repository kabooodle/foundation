<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\Comments::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->binary('uuid');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('commentable_parent_id')->unsigned();
            $table->integer('commentable_id')->unsigned();
            $table->string('commentable_type');
            $table->text('text');
            $table->text('text_raw');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdated('cascade');
        });

        DB::update("ALTER TABLE ".\Kabooodle\Models\Comments::getTableName()." AUTO_INCREMENT = 100433;");
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
