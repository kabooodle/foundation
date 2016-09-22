<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditChargeTypesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\CreditChargeTypes::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->decimal('credits_equiv', 10, 2);
            $table->decimal('per_credit', 10, 2);
            $table->boolean('active')->default(1);
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
