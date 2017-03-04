<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageviewsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement( 'CREATE VIEW v_pageviews AS 
        SELECT
 `views`.`viewable_type` AS `viewable_type`,
   `views`.`viewable_id` AS `viewable_id`,count(0) AS `count`
FROM `views` group by `views`.`viewable_id`,`views`.`viewable_type`' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement( 'DROP VIEW v_pageviews' );
    }
}
