<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventorySlugSeeder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('listables', function(Blueprint $table){
                $table->string('slug')->after('subclass_name')->nullable();
                $table->unique('slug', 'listables_unq_slug');
            });

            Artisan::call('db:seed', array('--class' => 'ListablesSlugSeeder'));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listables', function(Blueprint $table){
            $table->dropColumn('slug');
        });
    }
}
