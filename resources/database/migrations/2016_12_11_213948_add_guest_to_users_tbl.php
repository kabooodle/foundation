<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGuestToUsersTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('guest')->default(false)->after('activated');
        });

        DB::statement('ALTER TABLE `users` MODIFY COLUMN `password` VARCHAR(255) DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('guest');
        });

        DB::statement('ALTER TABLE `users` MODIFY COLUMN `password` VARCHAR(255) NOT NULL');
    }
}
