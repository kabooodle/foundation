<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditTransactionsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\CreditTransactions::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('transactable_type');
            $table->integer('transactable_id')->unsigned();
            $table->integer('amount')->default(0)->unsigned();
            $table->integer('transaction_amount');
            $table->integer('previous_balance_of');
            $table->enum('incr', ['+', '-'])->default('+');
            $table->enum('type', [\Kabooodle\Models\CreditTransactions::TYPE_CREDIT, \Kabooodle\Models\CreditTransactions::TYPE_DEBIT])->default(\Kabooodle\Models\CreditTransactions::TYPE_CREDIT);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'amount']);
        });

        Schema::table(\Kabooodle\Models\CreditTransactions::getTableName(), function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
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
