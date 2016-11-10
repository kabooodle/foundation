<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTransactionCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\Kabooodle\Models\ShippingTransactions::getTableName(), function(Blueprint $table){
            $table->dropColumn([
                'fulfilled',
                'fulfilled_on',
                'fulfilled_status'
            ]);
            $table->enum('shipping_status', ['CREATED', 'LABEL PRINTED', 'PROCESSING', 'IN TRANSIT', 'DELIVERED', 'RETURNED'])->default('CREATED')->nullable()->after('raw_response');
            $table->timestamp('shipping_status_updated_on')->default(null)->nullable()->after('raw_response');
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
