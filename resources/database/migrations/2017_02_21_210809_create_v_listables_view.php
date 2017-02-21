<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVListablesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement( 'CREATE VIEW v_listables AS 
        
        SELECT
   `inventory`.`id` AS `id`,\'Kabooodle\\\Models\\\Inventory\' AS `class`,
   `inventory`.`uuid` AS `uuid`,
   `inventory`.`inventory_sizes_id` AS `inventory_sizes_id`,
   `inventory`.`inventory_type_styles_id` AS `inventory_type_styles_id`,
   `inventory`.`inventory_type_id` AS `inventory_type_id`,
   `inventory`.`user_id` AS `user_id`,concat(`s`.`name`) AS `name`,concat(`s`.`name`,\' - \',`is`.`name`) AS `name_alt`,
   `inventory`.`description` AS `description`,NULL AS `locked`,
   `inventory`.`price_usd` AS `price_usd`,
   `inventory`.`wholesale_price_usd` AS `wholesale_price_usd`,
   `inventory`.`barcode` AS `barcode`,
   `inventory`.`initial_qty` AS `initial_qty`,
   `inventory`.`cover_photo_file_id` AS `cover_photo_file_id`,NULL AS `auto_add`,NULL AS `max_quantity`,
   `inventory`.`date_received` AS `date_received`,
   `inventory`.`created_at` AS `created_at`,
   `inventory`.`updated_at` AS `updated_at`,
   `inventory`.`deleted_by` AS `deleted_by`,
   `inventory`.`updated_by` AS `updated_by`,
   `inventory`.`created_by` AS `created_by`,
   `inventory`.`deleted_at` AS `deleted_at`
FROM ((`inventory` join `inventory_type_styles` `s` on((`s`.`id` = `inventory`.`inventory_type_styles_id`))) join `inventory_sizes` `is` on((`is`.`id` = `inventory`.`inventory_sizes_id`))) union select `inventory_groupings`.`id` AS `id`,\'Kabooodle\\\Models\\\InventoryGrouping\' AS `class`,`inventory_groupings`.`uuid` AS `uuid`,NULL AS `inventory_sizes_id`,NULL AS `inventory_type_styles_id`,NULL AS `inventory_type_id`,`inventory_groupings`.`user_id` AS `user_id`,`inventory_groupings`.`name` AS `name`,concat(\'Outfits\',\' - \',`inventory_groupings`.`name`) AS `name_alt`,`inventory_groupings`.`description` AS `description`,`inventory_groupings`.`locked` AS `locked`,`inventory_groupings`.`price_usd` AS `price_usd`,NULL AS `wholesale_price_usd`,`inventory_groupings`.`barcode` AS `barcode`,`inventory_groupings`.`initial_qty` AS `initial_qty`,`inventory_groupings`.`cover_photo_file_id` AS `cover_photo_file_id`,`inventory_groupings`.`auto_add` AS `auto_add`,`inventory_groupings`.`max_quantity` AS `max_quantity`,`inventory_groupings`.`date_received` AS `date_received`,`inventory_groupings`.`created_at` AS `created_at`,`inventory_groupings`.`updated_at` AS `updated_at`,`inventory_groupings`.`deleted_by` AS `deleted_by`,`inventory_groupings`.`updated_by` AS `updated_by`,`inventory_groupings`.`created_by` AS `created_by`,`inventory_groupings`.`deleted_at` AS `deleted_at` from `inventory_groupings`
        
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement( 'DROP VIEW v_listables' );
    }
}
