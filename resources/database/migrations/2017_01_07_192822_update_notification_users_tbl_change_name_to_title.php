<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNotificationUsersTblChangeNameToTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `notification_user_notices` CHANGE COLUMN `name` `title` VARCHAR(255) NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `notification_user_notices` CHANGE COLUMN `title` `name` VARCHAR(255) NOT NULL;');
    }
}
