<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kabooodle\Models\Notifications;

class AddClaimCanceledToNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Notifications::insert([
            'name' => 'claim_canceled',
            'description' => 'When a claim on one of your items is canceled',
            'active' => 1,
            'required_subscription_type' => 'merchant',
            'group' => 'claims',
            'type_email' => 1,
            'type_web' => 1,
            'type_sms' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
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
