<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        DB::update("ALTER TABLE notifications AUTO_INCREMENT = 328452;");
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
