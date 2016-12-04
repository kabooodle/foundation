<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_shipments', function(Blueprint $table){
            $table->increments('id');
            $table->binary('uuid');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('claim_id')->unsigned();
            $table->string('shipment_id');
            $table->bigInteger('recipient_id');
            $table->text('recipient_data');
            $table->string('sender_id');
            $table->text('sender_data');
            $table->string('parcel_id');
            $table->text('parcel_data');
            $table->enum('status', ['WAITING', 'QUEUED', 'SUCCESS', 'ERROR'])->default('WAITING');
            $table->enum('shipment_state', ['VALID', 'INCOMPLETE', 'INVALID']);
            $table->text('rates_url')->nullable();
            $table->text('rates_list')->nullable();
            $table->text('messages')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamp('expires_on')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'shipment_id']);
        });


        Schema::table('shipping_shipments', function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        DB::update("ALTER TABLE shipping_shipments AUTO_INCREMENT = 199121;");
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
