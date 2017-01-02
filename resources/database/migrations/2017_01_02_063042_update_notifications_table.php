<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kabooodle\Models\Notifications;

class UpdateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function(Blueprint $table){
            $table->enum('group', ['general', 'inventory', 'messenger', 'listings'])->after('active')->default('general');
            $table->string('required_subscription_type')->after('active')->nullable();
        });

        $r = Notifications::where('name', 'referral_joined')->first();
        $r->description = 'When someone you refer joins';
        $r->save();

        Notifications::whereIn('name', ['inventory_claimed', 'inventory_commented', 'inventory_updated'])->update(['group' => 'inventory']);
        Notifications::whereIn('name', ['referral_joined'])->update(['group' => 'general']);

        Notifications::create([
            'name' => 'thread_message_added',
            'description' => 'When a new response is added to an existing message',
            'active' => 1,
            'group' => 'messenger'
        ]);

        Notifications::create([
            'name' => 'thread_created',
            'description' => 'When a brand new message is received',
            'active' => 1,
            'group' => 'messenger'
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
        Schema::table('notifications', function(Blueprint $table){
           $table->dropColumn(['group', 'required_subscription_type']);
        });
    }
}
