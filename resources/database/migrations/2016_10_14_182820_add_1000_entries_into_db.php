<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add1000EntriesIntoDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $type = \Kabooodle\Models\InventoryType::LuLaRoe()->WithStylesAndSizes()->first();

        for($i=0; $i<2000; $i++) {
            $style = $type->styles->random();
            $size = $style->sizes->random();
            \Kabooodle\Models\Inventory::create([
                'user_id' => 152295,
                'inventory_type_id' => 188432,
                'inventory_type_styles_id' => $style->id,
                'inventory_sizes_id' => $size->id,
                'description' => str_random(100),
                'barcode' => null,
                'initial_qty' => rand(0,20),
                'price_usd' => rand(0,75)
            ]);
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
