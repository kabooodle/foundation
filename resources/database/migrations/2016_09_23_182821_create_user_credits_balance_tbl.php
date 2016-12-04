<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCreditsBalanceTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_balance', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->decimal('balance', 10,2);
            $table->decimal('previous_balance_of', 10, 2);
            $table->decimal('last_transaction_amount_of', 10, 2);
            $table->integer('version')->unsigned()->default(0);
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
