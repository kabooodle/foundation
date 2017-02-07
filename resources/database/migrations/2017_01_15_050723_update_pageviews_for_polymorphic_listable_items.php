<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePageviewsForPolymorphicListableItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pageviews', function (Blueprint $table) {
            $table->dropForeign('pageviews_inventory_id_foreign');
        });

        Schema::table('pageviews', function (Blueprint $table) {
            $table->string('listable_type')->after('shoppable_id');
        });

        DB::statement('ALTER TABLE `pageviews` CHANGE COLUMN `inventory_id` `listable_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('UPDATE `pageviews` SET `listable_type` = "Kabooodle\\Models\\Inventory"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `pageviews` CHANGE COLUMN `listable_id` `inventory_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('pageviews', function (Blueprint $table) {
            $table->dropColumn('listable_type');

            $table->foreign('inventory_id')
                ->references('id')->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
}
