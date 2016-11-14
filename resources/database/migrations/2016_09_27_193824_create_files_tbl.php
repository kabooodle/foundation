<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function(Blueprint $table){
            $table->increments('id');
            $table->string('fileable_id');
            $table->string('fileable_type');
            $table->string('bucket_name');
            $table->string('location');
            $table->string('key');
            $table->mediumInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::update("ALTER TABLE files AUTO_INCREMENT = 199421;");
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
