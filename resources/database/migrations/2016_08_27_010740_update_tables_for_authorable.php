<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesForAuthorable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['flashsales', 'inventory', 'groups'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->bigInteger('created_by')->unsigned()->nullable()->after('updated_at');
                $table->bigInteger('updated_by')->unsigned()->nullable()->after('updated_at');
                $table->bigInteger('deleted_by')->unsigned()->nullable()->after('updated_at');
            });

            Schema::table($tableName, function(Blueprint $table){
                $table->foreign('created_by')
                    ->references('id')->on('users')
                    ->onDelete('cascade')
                    ->onUpdated('cascade');

                $table->foreign('updated_by')
                    ->references('id')->on('users')
                    ->onDelete('cascade')
                    ->onUpdated('cascade');

                $table->foreign('deleted_by')
                    ->references('id')->on('users')
                    ->onDelete('cascade')
                    ->onUpdated('cascade');
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
        //
    }
}
