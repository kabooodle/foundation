<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InventoryTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index()->unsigned();
            $table->integer('inventory_category_id')->nullable()->unsigned();
            $table->string('name');
            $table->string('description');
            $table->enum('size', [''])->nullable();
            $table->string('barcode')->nullable();
            $table->smallInteger('initial_qty')->default(0)->nullable();
            $table->smallInteger('current_qty')->default(0)->nullable();
            $table->timestamp('date_received')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('inventory', function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        DB::update("ALTER TABLE inventory AUTO_INCREMENT = 8099315;");
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
