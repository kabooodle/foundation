<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsRemoveClaimAddShipmentClaims extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\Kabooodle\Models\ShippingShipments::getTableName(), function(Blueprint $table){
            $table->dropColumn('claim_id');
        });

        Schema::create('shipping_shipments_claims', function(Blueprint $table){
            $table->increments('id');
            $table->integer('shipping_shipments_id');
            $table->integer('claim_id');
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
