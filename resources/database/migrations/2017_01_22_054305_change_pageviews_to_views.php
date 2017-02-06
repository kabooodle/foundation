<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePageviewsToViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `pageviews` CHANGE COLUMN `shoppable_id` `viewable_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `pageviews` CHANGE COLUMN `shoppable_type` `viewable_type` VARCHAR(255) NOT NULL');

        Schema::table('pageviews', function (Blueprint $table) {
            $table->bigInteger('viewer_id')->unsigned()->nullable()->default(NULL)->after('id');
            $table->bigInteger('parent_id')->nullable()->default(NULL)->after('viewable_id');
            $table->dropColumn('listable_id');
            $table->dropColumn('listable_type');
            $table->rename('views');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `views` CHANGE COLUMN `viewable_id` `shoppable_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `views` CHANGE COLUMN `viewable_type` `shoppable_type` VARCHAR(255) NOT NULL');

        Schema::table('views', function (Blueprint $table) {
            $table->dropColumn('viewer_id');
            $table->dropColumn('parent_id');
            $table->string('listable_type')->after('shoppable_id');
            $table->bigInteger('listable_id')->after('listable_type');
            $table->rename('pageviews');
        });
    }
}
