<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInventoryTblAddTypeStyleSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function(Blueprint $table){
            $table->dropColumn([
                'inventory_category_id',
                'size'
            ]);

            $table->integer('inventory_type_id')->unsigned()->after('id');
            $table->integer('inventory_type_styles_id')->unsigned()->after('id');
            $table->integer('inventory_sizes_id')->unsigned()->after('id');

            $table->foreign('inventory_type_id')
                ->references('id')->on('inventory_type')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('inventory_type_styles_id')
                ->references('id')->on(\Kabooodle\Models\InventoryTypeStyles::getTableName())
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('inventory_sizes_id')
                ->references('id')->on(\Kabooodle\Models\InventorySizes::getTableName())
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
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
