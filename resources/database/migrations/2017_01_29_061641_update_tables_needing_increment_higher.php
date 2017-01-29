<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesNeedingIncrementHigher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("ALTER TABLE inventory AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE listings AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE listing_items AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE addresses AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE claims AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE comments AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE facebook_nodes AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE flashsales AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE flashsales AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE flashsales_admins AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE flashsales_groups AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE flashsales_groups_users AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE flashsales_sellers_groups AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE followables AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE inventory_categories AUTO_INCREMENT = ".rand(1019199, 1219199).";");
        DB::update("ALTER TABLE messenger_messages AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE messenger_participants AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE messenger_threads AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE notification_user_notices AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE phone_numbers AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE shipping_parcel_templates AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE shipping_queue AUTO_INCREMENT = ".rand(1019199, 1219199).";");
        DB::update("ALTER TABLE shipping_shipments AUTO_INCREMENT = ".rand(1019199, 1219199).";");
        DB::update("ALTER TABLE shipping_shipments_claims AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE shipping_transactions AUTO_INCREMENT = ".rand(10191999, 12191999).";");
        DB::update("ALTER TABLE subscriptions AUTO_INCREMENT = ".rand(1019199, 1219199).";");
        DB::update("ALTER TABLE shipping_transactions AUTO_INCREMENT = ".rand(1019199, 1219199).";");
        DB::update("ALTER TABLE watchables AUTO_INCREMENT = ".rand(10197199, 12197199).";");
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
