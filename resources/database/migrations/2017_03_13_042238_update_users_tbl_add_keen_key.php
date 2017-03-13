<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTblAddKeenKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('users', function(Blueprint $table){
                $table->text('keen_access_key')->after('stripe_id')->nullable()->default(null);
            });

            Artisan::call('db:seed', array('--class' => 'KeenReadKeySeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('keen_access_key');
        });
    }
}
