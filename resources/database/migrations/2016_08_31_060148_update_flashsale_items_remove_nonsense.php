<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFlashsaleItemsRemoveNonsense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flashsale_items', function(Blueprint $table){

            $table->dropForeign('flashsale_items_created_by_foreign');
            $table->dropForeign('flashsale_items_updated_by_foreign');

            if (Schema::hasColumn('flashsale_items', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('flashsale_items', 'base_price')) {
                $table->dropColumn('base_price');
            }
            if (Schema::hasColumn('flashsale_items', 'base_price_discount')) {
                $table->dropColumn('base_price_discount');
            }
            if (Schema::hasColumn('flashsale_items', 'enabled')) {
                $table->dropColumn('enabled');
            }
            if (Schema::hasColumn('flashsale_items', 'enable_on')) {
                $table->dropColumn('enable_on');
            }
            if (Schema::hasColumn('flashsale_items', 'created_by')) {
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('flashsale_items', 'updated_by')) {
                $table->dropColumn('updated_by');
            }

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
