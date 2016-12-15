<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserShippingFromAddressTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_addresses', function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->enum('type', ['ship_from', 'ship_to'])->default('ship_from');
            $table->string('company')->nullable();
            $table->string('street1');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->enum('state', array_keys(getStateAbbrevs()));
            $table->string('country')->default('US');
            $table->string('zip');
            $table->string('phone')->nullable()->default(null);
            $table->tinyInteger('is_residential')->default(1);
            $table->text('metadata')->nullable();
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
        //
    }
}
