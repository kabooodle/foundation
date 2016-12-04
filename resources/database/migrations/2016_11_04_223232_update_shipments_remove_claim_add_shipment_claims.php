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
            $table->integer('shipping_shipments_id')->unsigned();
            $table->bigInteger('claim_id')->unsigned();

            $table->foreign('shipping_shipments_id')
                ->references('id')->on('shipping_shipments')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('claim_id', 'shipping_shipments_claims_id_foreign')
                ->references('id')->on('claims')
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
