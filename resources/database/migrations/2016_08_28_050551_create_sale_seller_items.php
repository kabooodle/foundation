<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleSellerItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flashsale_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('flashsale_id')->unsigned();
            $table->bigInteger('seller_id')->unsigned();
            $table->bigInteger('inventory_id')->unsigned();
            $table->integer('quantity');
            $table->float('base_price');
            $table->integer('base_price_discount')->nullable();
            $table->tinyInteger('enabled')->default(0);
            $table->timestamp('enable_on')->nullable();
            $table->bigInteger('created_by')->unsigned();
            $table->bigInteger('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['flashsale_id', 'seller_id', 'inventory_id']);
        });

        Schema::table('flashsale_items', function (Blueprint $table) {
            $table->foreign('flashsale_id')
                ->references('id')->on('flashsales')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('updated_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('inventory_id')
                ->references('id')->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('seller_id')
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
