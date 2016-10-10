<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventorySizesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\InventorySizes::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        DB::update("ALTER TABLE ".\Kabooodle\Models\InventorySizes::getTableName()." AUTO_INCREMENT = 193234;");

        $sql = "
INSERT INTO `inventory_sizes` (`id`, `name`, `slug`, `sort_order`, `deleted_at`, `created_at`, `updated_at`)
VALUES
	(193234, '2', '2', 0, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193235, '4', '4', 1, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193236, '6', '6', 2, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193237, '8', '8', 3, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193238, '10', '10', 4, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193239, '12', '12', 5, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193240, '14', '14', 6, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193241, '1C', '1c', 0, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193242, '2C', '2c', 1, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193243, '2XL', '2xl', 6, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193244, '3C', '3c', 3, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193245, '3XL', '3xl', 7, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193246, 'L', 'l', 4, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193247, 'L/XL', 'l-xl', 1, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193248, 'M', 'm', 3, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193249, 'One Size', 'one-size', 1, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193250, 'S', 's', 2, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193251, 'S/M', 's-m', 0, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193252, 'Tall & Curvy', 'tall-curvy', 2, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193253, 'Tween', 'tween', 0, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193254, 'XL', 'xl', 5, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193255, 'XS', 'xs', 1, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44'),
	(193256, 'XXS', 'xxs', 0, NULL, '2016-10-01 03:15:44', '2016-10-01 03:15:44');
";
        DB::statement(DB::raw($sql));

//        $sizes = [2,4,6,8,10,12,14,"1C","2C","3C","L/XL","S/M","2XL","3XL","L","M","One Size","S","Tall & Curvy","Tween","XL","XS","XXS"];
//        foreach($sizes as $size) {
//            \Kabooodle\Models\InventorySizes::create([
//                'name' => $size,
//                'slug' => str_slug($size),
//                'sort_order' => 0
//            ]);
//        }
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
