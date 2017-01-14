<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kabooodle\Models\Notifications;

class UpdateNotificationsPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function(Blueprint $table){
            $table->tinyInteger('type_web')->default(1)->after('group');
            $table->tinyInteger('type_email')->default(1)->after('group');
            $table->tinyInteger('type_sms')->default(0)->after('group');
        });

        Schema::table('users_notificationsettings', function(Blueprint $table){
            $table->tinyInteger('sms')->default(0)->after('web');
        });

        $r = Notifications::where('name', 'inventory_updated')->first();
        $r->type_sms = true;
        $r->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_notificationsettings', function(Blueprint $table){
            $table->dropColumn('sms');
        });
    }
}
