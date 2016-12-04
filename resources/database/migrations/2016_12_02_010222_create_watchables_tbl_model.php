<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWatchablesTblModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watchables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('watchable_id');
            $table->string('watchable_type');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('watchables', function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdated('cascade');
        });

        DB::update("ALTER TABLE watchables AUTO_INCREMENT = 1919101;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('watchables');
    }
}
