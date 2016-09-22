<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippingTransactionsAddRateDataCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\Kabooodle\Models\ShippingTransactions::getTableName(), function(Blueprint $table){
            $table->decimal('rate_amount', 10,2)->after('rate_id');
            $table->decimal('rate_amount_addon', 10,2)->after('rate_id');
            $table->decimal('rate_final_amount', 10,2)->after('rate_id');
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
