<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCouponsAddCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_coupons', function(Blueprint $table){
            $table->string('coupon_id')->after('name');
            $table->integer('coupon_amount_off')->default(null)->after('coupon_id')->unsigned()->nullable();
            $table->enum('type', ['cents', 'percent'])->default('percent')->after('coupon_amount_off');
            $table->integer('max_redemptions')->unsigned()->default(null)->nullable()->after('active');
            $table->dropColumn('stripe_id');
            $table->dropColumn('coupon_amount_cents');
        });

        Schema::table('subscription_coupon_users', function(Blueprint $table){
            $table->integer('subscription_id')->unsigned()->after('id');
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
