<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('subscription_coupons')) {
            Schema::create('subscription_coupons', function(Blueprint $table){
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('stripe_id');
                $table->integer('coupon_amount_cents')->default(null)->unsigned()->nullable();
                $table->tinyInteger('active')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }


        Schema::create('subscription_coupon_users', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('subscription_coupon_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->text('couponable_type')->nullable();
            $table->bigInteger('couponable_id')->nullable()->unsigned();
            $table->timestamp('coupon_applied_on')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('subscription_coupon_id')
                ->references('id')->on('subscription_coupons')
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
        Schema::dropIfExists('subscription_coupons');
        Schema::dropIfExists('subscription_coupon_users');
    }
}
