<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingTransactionsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_transactions', function(Illuminate\Database\Schema\Blueprint $table){
            $table->increments('id');
            $table->binary('uuid');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('shipping_shipments_id')->unsigned();
            $table->binary('shipping_shipments_uuid');
            $table->string('rate_id');
            $table->text('rate_data');
            $table->string('transaction_id');
            $table->string('tracking_number');
            $table->text('tracking_status');
            $table->string('tracking_url_provider');
            $table->text('tracking_history')->nullable();
            $table->string('label_url');
            $table->binary('label_file_embedded')->nullable();
            $table->enum('status', ['WAITING', 'QUEUED', 'SUCCESS', 'ERROR', 'REFUNDED', 'REFUNDPENDING', 'REFUNDREJECTED'])->default('WAITING');
            $table->text('messages')->nullable();
            $table->text('raw_response');
            $table->timestamps();

            $table->index(['user_id']);
        });


        Schema::table('shipping_transactions', function(Illuminate\Database\Schema\Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('shipping_shipments_id')
                ->references('id')->on('shipping_shipments')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        DB::update("ALTER TABLE shipping_transactions AUTO_INCREMENT = 190921;");
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
