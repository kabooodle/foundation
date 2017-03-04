<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameAltToListablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listables', function (Blueprint $table) {
            $table->string('name_alt')->after('name');
        });

        DB::statement('UPDATE `listables` l JOIN `inventory_type_styles` styles ON l.`inventory_type_styles_id` = styles.`id` JOIN `inventory_sizes` sizes ON l.`inventory_sizes_id` = sizes.`id` SET `name_alt` = CONCAT(styles.`name`, " - ", sizes.`name`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listables', function (Blueprint $table) {
            $table->dropColumn('name_alt');
        });
    }
}
