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
        Schema::create(\Kabooodle\Models\ShippingAddress::getTableName(), function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->enum('type', [\Kabooodle\Models\ShippingAddress::TYPE_FROM, \Kabooodle\Models\ShippingAddress::TYPE_TO])->default(\Kabooodle\Models\ShippingAddress::TYPE_FROM);
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
