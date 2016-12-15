<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('address')->unique();
            $table->boolean('primary')->default(false);
            $table->boolean('verified')->default(false);
            $table->string('token', 60)->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('emails', function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        DB::statement('INSERT INTO `emails` (`user_id`, `address`, `primary`, `created_at`, `updated_at`) SELECT `users`.`id`, `users`.`email`, 1, NOW(), NOW() FROM `users`');

        Schema::table('users', function(Blueprint $table){
            $table->dropUnique('users_email_unique');
            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table){
            $table->string('email')->unique()->nullable()->after('name');
        });
        DB::statement('UPDATE `users` JOIN `emails` ON `emails`.`user_id` = `users`.`id` SET `users`.`email` = `emails`.`address` WHERE `emails`.`primary` = 1');
        Schema::drop('emails');
    }
}
