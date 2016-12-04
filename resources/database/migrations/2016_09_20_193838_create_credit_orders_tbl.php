<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditOrdersTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_receipts', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('credit_charge_type_id')->unsigned();
            $table->string('stripe_invoice_id');
            $table->string('stripe_charge_id');
            $table->text('transaction_items');
            $table->integer('transaction_amount_cents');
            $table->integer('transaction_amount_dollars');
            $table->text('stripe_raw_response');
            $table->timestamps();
        });
        DB::update("ALTER TABLE credit_receipts AUTO_INCREMENT = 190309;");

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
