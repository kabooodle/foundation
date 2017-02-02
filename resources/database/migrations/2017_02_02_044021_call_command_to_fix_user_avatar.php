<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CallCommandToFixUserAvatar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = \Kabooodle\Models\User::all();
        foreach($users as $user) {
            $event = new \Kabooodle\Bus\Events\User\UserWasCreatedEvent($user, '');
            $handler = new \Kabooodle\Bus\Handlers\Events\User\AssignUserGenericAvatar;
            $handler->handle($event);
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
