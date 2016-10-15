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

        $models = [];
        $user = \Kabooodle\Models\User::first();
        for($i=0; $i<10000; $i++) {
            $style = $type->styles->random();
            $size = $style->sizes->random();
            $models[] = new \Kabooodle\Models\Inventory([
                'inventory_type_id' => 188432,
                'inventory_type_styles_id' => $style->id,
                'inventory_sizes_id' => $size->id,
                'description' => str_random(100),
                'barcode' => null,
                'initial_qty' => rand(0,20),
                'price_usd' => rand(0,75)
            ]);
        }

        $user->inventory()->saveMany($models);
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
