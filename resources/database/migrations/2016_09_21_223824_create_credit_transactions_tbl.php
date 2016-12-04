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
        Schema::create(\Kabooodle\Models\CreditTransactionsLog::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('transactable_type');
            $table->integer('transactable_id')->unsigned();
            $table->decimal('abs_amount', 10, 2)->default(0);
            $table->decimal('transaction_amount', 10, 2)->default(0);
            $table->decimal('previous_balance_of', 10, 2)->default(0);
            $table->enum('incr', ['+', '-'])->default('+');
            $table->enum('type', [\Kabooodle\Models\CreditTransactionsLog::TYPE_CREDIT, \Kabooodle\Models\CreditTransactionsLog::TYPE_DEBIT])->default(\Kabooodle\Models\CreditTransactionsLog::TYPE_CREDIT);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'transaction_amount']);
        });

        Schema::table(\Kabooodle\Models\CreditTransactionsLog::getTableName(), function(Blueprint $table){
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
