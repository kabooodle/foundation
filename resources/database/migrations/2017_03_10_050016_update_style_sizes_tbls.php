<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kabooodle\Models\InventoryTypeStyles;

class UpdateStyleSizesTbls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function(){

            $its = InventoryTypeStyles::create([
                'inventory_type_id' => 188432,
                'name' => 'Mimi',
                'slug' => 'mimi',
                'sort_order' => 25,
                'wholesale_price_usd_less_5_percent' => '32.30',
                'wholesale_price_usd' => '34',
                'suggested_price_usd' => '75.00'
            ]);

            DB::statement('INSERT into inventory_styles_sizes (inventory_type_style_id, inventory_size_id) values ('.$its->id.', 193249)');

            $joy = InventoryTypeStyles::findOrFail(184326);
            $joy->wholesale_price_usd = 25;
            $joy->wholesale_price_usd_less_5_percent = '23.75';
            $joy->save();

            $ana = InventoryTypeStyles::findOrFail(184315);
            $ana->wholesale_price_usd = 27;
            $ana->wholesale_price_usd_less_5_percent = '25.65';
            $ana->save();

            $leggings = InventoryTypeStyles::findOrFail(184330);
            $leggings->wholesale_price_usd = '8.50';
            $leggings->wholesale_price_usd_less_5_percent = '8.075';
            $leggings->save();

            Artisan::call('cache:clear');
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
