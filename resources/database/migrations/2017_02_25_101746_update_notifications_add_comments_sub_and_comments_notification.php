<?php

use Carbon\Carbon;
use Kabooodle\Models\Notifications;
use Illuminate\Database\Migrations\Migration;

class UpdateNotificationsAddCommentsSubAndCommentsNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE notifications CHANGE `group` `group`  ENUM('claims', 'comments','flashsales', 'general', 'inventory','listings', 'messenger')");

//        Notifications::findOrFail(328454)->update([
//            'name' => 'commented_on_item',
//            'group' => 'comments',
//        ]);

        Notifications::insert([
            'name' => 'comment_mentioned',
            'description' => 'When someone mentions you in a comment',
            'active' => 1,
            'required_subscription_type' => null,
            'group' => 'comments',
            'type_email' => 1,
            'type_web' => 1,
            'type_sms' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
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
