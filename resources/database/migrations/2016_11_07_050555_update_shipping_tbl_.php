<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippingTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\Kabooodle\Models\ShippingShipments::getTableName(), function(Blueprint $table){
            $table->integer('shipping_parcel_template_id')->unsigned()->index()->after('user_id')->nullable();
        });

        Schema::table(\Kabooodle\Models\ShippingShipments::getTableName(), function(Blueprint $table){
            $table->foreign('shipping_parcel_template_id', 'shipping_fk')
                ->references('id')->on('shipping_parcel_templates')
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
