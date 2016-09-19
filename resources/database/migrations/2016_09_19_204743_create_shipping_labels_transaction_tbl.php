<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingLabelsTransactionTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\ShippingLabelsTransactions::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('shipping_transaction_id')->unsigned();
            $table->integer('quantity')->default(0)->unsigned();
            $table->integer('transaction_quantity');
            $table->string('source');
            $table->enum('incr', ['+', '-'])->default('+');
            $table->enum('type', [\Kabooodle\Models\ShippingLabelsTransactions::TYPE_CREDIT, \Kabooodle\Models\ShippingLabelsTransactions::TYPE_DEBIT])->default(\Kabooodle\Models\ShippingLabelsTransactions::TYPE_CREDIT);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'shipping_transaction_id'], 'user_id_shipping_transaction_id_idx');
        });

        Schema::table(\Kabooodle\Models\ShippingLabelsTransactions::getTableName(), function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

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
