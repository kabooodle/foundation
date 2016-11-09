<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippingTxnAddLabelColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\Kabooodle\Models\ShippingTransactions::getTableName(), function(Blueprint $table){
            $table->tinyInteger('fulfilled')->default(0)->after('raw_response');
            $table->enum('fulfilled_status', ['UNKNOWN', 'IN TRANSIT', 'DELIVERED', 'RETURNED'])->default(null)->nullable()->after('raw_response');
            $table->timestamp('fulfilled_on')->default(null)->nullable()->after('raw_response');
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
