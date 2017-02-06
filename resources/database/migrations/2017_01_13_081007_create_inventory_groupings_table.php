<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryGroupingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_groupings', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('uuid');
            $table->bigInteger('user_id')->index()->unsigned();
            $table->string('name');
            $table->string('description');
            $table->boolean('locked')->default(true);
            $table->decimal('price_usd', 6,2);
            $table->string('barcode')->nullable();
            $table->smallInteger('initial_qty')->default(0)->nullable();
            $table->timestamp('date_received')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
        });

        Schema::table('inventory_groupings', function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdated('cascade');

            $table->foreign('updated_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdated('cascade');

            $table->foreign('deleted_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdated('cascade');
        });

        DB::update("ALTER TABLE inventory_groupings AUTO_INCREMENT = 8099315;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('inventory_groupings');
    }
}
