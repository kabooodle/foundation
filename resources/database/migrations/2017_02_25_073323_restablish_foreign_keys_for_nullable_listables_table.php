<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestablishForeignKeysForNullableListablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('listables', function (Blueprint $table) {
            $table->dropForeign('inventory_inventory_type_id_foreign');
            $table->dropForeign('inventory_inventory_type_styles_id_foreign');
            $table->dropForeign('inventory_inventory_sizes_id_foreign');
        });

        Schema::table('listables', function (Blueprint $table) {
            $table->foreign('inventory_type_id')
                ->references('id')->on('inventory_type')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('inventory_type_styles_id')
                ->references('id')->on('inventory_type_styles')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('inventory_sizes_id')
                ->references('id')->on('inventory_sizes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listables', function (Blueprint $table) {
            //
        });
    }
}
