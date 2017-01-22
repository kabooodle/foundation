<?php

use Kabooodle\Models\Notifications;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNotificationsTblAddFlashsaleNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE notifications CHANGE `group` `group`  ENUM('general','inventory','messenger','listings','claims','flashsales')");

        Notifications::create([
            'name' => 'flashsale_invited_as_seller',
            'description' => 'When you are invited as a seller to a flash sale',
            'active' => 1,
            'group' => 'flashsales',
            'type_email' => 1,
            'type_sms' => 0,
            'required_subscription_type' => 'merchant'
        ]);

        Notifications::create([
            'name' => 'flashsale_invited_as_admin',
            'description' => 'When you are invited as an admin to a flash sale',
            'active' => 1,
            'group' => 'flashsales',
            'type_email' => 1,
            'type_sms' => 0
        ]);

        Artisan::call('cache:clear');
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
