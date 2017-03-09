<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersAddSocialLinksAndAbout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table){
            $table->text('about_me')->nullable()->after('timezone')->default(null);
            $table->string('social_twitter')->nullable()->default(null)->after('remember_token');
            $table->string('social_website')->nullable()->default(null)->after('remember_token');
            $table->string('social_instragram')->nullable()->default(null)->after('remember_token');
            $table->string('social_youtube')->nullable()->default(null)->after('remember_token');
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
           $table->dropColumn([
               'about_me',
               'social_twitter',
               'social_website',
               'social_instagram',
               'social_youtube'
           ]);
        });
    }
}
