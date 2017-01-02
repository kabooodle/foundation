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
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('group', ['general', 'inventory', 'messenger', 'listings', 'claims'])->after('active')->default('general');
            $table->string('required_subscription_type')->after('active')->nullable();
        });

        $r = Notifications::where('name', 'referral_joined')->first();
        $r->description = 'When someone you refer joins';
        $r->save();

        $r = Notifications::where('name', 'inventory_claimed')->first();
        $r->description = 'When a claim is made on your inventory item';
        $r->save();

        Notifications::whereIn('name', ['inventory_claimed', 'inventory_commented', 'inventory_updated'])->update(['group' => 'inventory']);
        Notifications::whereIn('name', ['inventory_claimed', 'inventory_commented'])->update(['required_subscription_type' =>'merchant']);
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

        Notifications::create([
            'name' => 'my_claim_rejected',
            'description' => 'When an item you have claimed is rejected or cancelled',
            'active' => 1,
            'group' => 'claims'
        ]);

        Notifications::create([
            'name' => 'my_claim_accepted',
            'description' => 'When an item you have claimed is accepted',
            'active' => 1,
            'group' => 'claims'
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
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['group', 'required_subscription_type', 'type_web', 'type_email', 'type_sms']);
        });

        Notifications::whereIn('name', ['thread_message_added', 'thread_created', 'my_claim_rejected', 'my_claim_accepted'])->delete();
    }
}
