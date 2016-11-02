<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notifications = [
            'referral_joined' => 'When someone you referred joined',
            'inventory_claimed' => 'When someone claims one of your inventory items',
            'inventory_commented' => 'When someone comments on your inventory item',
            'inventory_updated' => 'When an inventory item you are watching is updated',
        ];

        foreach($notifications as $name => $description) {
            \Kabooodle\Models\Notifications::create([
                'name' => $name,
                'description' => $description
            ]);
        }
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
