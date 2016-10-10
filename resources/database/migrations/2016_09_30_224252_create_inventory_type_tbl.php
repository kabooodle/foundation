<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTypeTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\InventoryType::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description');
            $table->boolean('active')->default(1);
            $table->integer('sort_order');
            $table->softDeletes();
            $table->timestamps();
        });

        DB::update("ALTER TABLE ".\Kabooodle\Models\InventoryType::getTableName()." AUTO_INCREMENT = 188432;");

        $inserts = ['LuLaRoe', 'Custom'];
        foreach($inserts as $k => $insert){
            \Kabooodle\Models\InventoryType::create([
                'name' => $insert,
                'slug' => str_slug($insert),
                'active' => 1,
                'sort_order' => $k
            ]);
        }

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
