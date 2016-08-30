<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create(\Kabooodle\Models\FlashsaleSellerStore::getTableName(), function (Blueprint $table) {
//            $table->increments('id');
//            $table->integer('flashsale_id')->unsigned();
//            $table->integer('seller_id')->unsigned();
//            $table->string('name');
//            $table->text('description');
//            $table->text('policies')->nullable();
//            $table->tinyInteger('enabled')->default(0);
//            $table->integer('created_by')->unsigned();
//            $table->integer('updated_by')->unsigned();
//            $table->timestamps();
//            $table->softDeletes();
//
//            $table->index(['flashsale_id', 'seller_id']);
//        });
//
//        Schema::table(\Kabooodle\Models\FlashsaleSellerStore::getTableName(), function (Blueprint $table) {
//            $table->foreign('flashsale_id')
//                ->references('id')->on('flashsales')
//                ->onDelete('cascade')
//                ->onUpdated('cascade');
//
//            $table->foreign('created_by')
//                ->references('id')->on('users')
//                ->onDelete('cascade')
//                ->onUpdated('cascade');
//
//            $table->foreign('updated_by')
//                ->references('id')->on('users')
//                ->onDelete('cascade')
//                ->onUpdated('cascade');
//
//            $table->foreign('seller_id')
//                ->references('id')->on('users')
//                ->onDelete('cascade')
//                ->onUpdated('cascade');
//        });
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
