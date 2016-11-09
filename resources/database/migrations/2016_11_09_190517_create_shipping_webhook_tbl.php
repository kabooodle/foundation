<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingWebhookTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\ShippingWebhooks::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->integer('shipping_transaction_id')->unsigned();
            $table->longText('data')->nullable();
            $table->timestamps();

            $table->foreign('shipping_transaction_id')
                                ->references('id')->on('shipping_transactions')
                                ->onDelete('cascade')
                                ->onUpdate('cascade');
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
