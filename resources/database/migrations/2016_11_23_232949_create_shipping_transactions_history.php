<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kabooodle\Models\ShippingTransactionHistory;

class CreateShippingTransactionsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ShippingTransactionHistory::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->integer('shipping_transaction_id')->unsigned()->nullable();
            $table->text('payload');
            $table->enum('status', ShippingTransactionHistory::STATII);
            $table->string('status_details')->nullable();
            $table->dateTime('status_date');
            $table->text('status_location');
            $table->text('tracking_history');
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
        Schema::drop(ShippingTransactionHistory::getTableName());
    }
}
