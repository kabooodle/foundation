<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUserForeignKeyFromRevisionsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $keyExists = DB::select( DB::raw( 'SHOW KEYS FROM revisions WHERE Key_name="revisions_user_foreign"'));

        if ($keyExists) {
            Schema::table('revisions', function (Blueprint $table) {
                $table->dropForeign('revisions_user_foreign');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->foreign('user')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdated('cascade');
        });
    }
}
