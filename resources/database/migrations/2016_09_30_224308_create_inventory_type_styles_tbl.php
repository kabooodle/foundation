<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTypeStylesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\InventoryTypeStyles::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->integer('inventory_type_id')->unsigned()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('inventory_type_id')
                ->references('id')->on(\Kabooodle\Models\InventoryType::getTableName())
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });


        DB::update("ALTER TABLE ".\Kabooodle\Models\InventoryTypeStyles::getTableName()." AUTO_INCREMENT = 184313;");

        $llrTypeId = \Kabooodle\Models\InventoryType::where('slug', 'lularoe')->first();
        $llrStyles = ["Amelia","Ana","Azure","Carly","Cassie","Classic T","Irma","Jade","Jill","Jordan","Joy","Julia","Leggings","Lindsey","Lola","Lucy","Madison","Maxi","Monroe","Nicole","Perfect T","Randy","Sarah","Adeline","Bianka","Gracie","Kids Azure","Kids Leggings","Mae","Sloan","Mark","Patrick"];
        sort($llrStyles, SORT_NATURAL | SORT_FLAG_CASE);
        foreach($llrStyles as $k => $llrStyle) {
            \Kabooodle\Models\InventoryTypeStyles::create([
                'inventory_type_id' => $llrTypeId->id,
                'name' => $llrStyle,
                'slug' => str_slug($llrStyle),
                'sort_order' => $k
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
