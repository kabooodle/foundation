<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleanupCouponMigrationsAndSeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('subscription_coupon_users');
        Schema::dropIfExists('subscription_coupons');

        Schema::create('referrals', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('referred_by_id')->unsigned();
            $table->bigInteger('referred_id')->unsigned();
            $table->string('stripe_coupon_id')->nullable()->default(null);
            $table->string('group_hash')->nullable()->default(null);
            $table->timestamp('coupon_applied_at')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('referred_by_id', 'user_referrals_referred_by_fk')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('referred_id', 'user_referrals_referred_fk')
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
        Schema::drop('referrals');
    }
}
