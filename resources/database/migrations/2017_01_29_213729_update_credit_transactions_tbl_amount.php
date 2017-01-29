<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCreditTransactionsTblAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("ALTER TABLE credit_transactions_logs AUTO_INCREMENT = ".rand(10197199, 12197199).";");
        Schema::table('credit_transactions_logs', function(Blueprint $table){
            $table->binary('uuid')->after('id');
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
