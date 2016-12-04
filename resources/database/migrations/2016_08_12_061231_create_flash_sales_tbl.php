<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlashSalesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flashsales', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('user_id')->index()->unsigned();
            $table->enum('type', ['single','group'])->default('single');
            $table->string('name');
            $table->text('description');
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->timestamp('discount_starts_at')->nullable();
            $table->timestamp('discount_ends_at')->nullable();
            $table->boolean('active')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('flashsales', function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        DB::update("ALTER TABLE flashsales AUTO_INCREMENT = 193243;");
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
