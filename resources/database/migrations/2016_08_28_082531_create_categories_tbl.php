<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('sort_order')->default(0);
        });

        DB::statement('INSERT INTO `categories` (`id`, `name`, `parent_id`, `sort_order`)
VALUES
	(1, \'Adeline\', NULL, 1),
	(2, \'Amelia\', NULL, 2),
	(3, \'Ana\', NULL, 3),
	(4, \'Azure\', NULL, 4),
	(5, \'Bianka\', NULL, 5),
	(6, \'Carly\', NULL, 6),
	(7, \'Cassie\', NULL, 7),
	(8, \'Classic Tee\', NULL, 8),
	(9, \'Gracie\', NULL, 9),
	(10, \'Gracie Romper\', NULL, 10),
	(11, \'Irma\', NULL, 11),
	(12, \'Jade\', NULL, 12),
	(13, \'Jill\', NULL, 13),
	(14, \'Jordan\', NULL, 14),
	(15, \'Joy\', NULL, 15),
	(16, \'Julia\', NULL, 16),
	(17, \'Kids Azure\', NULL, 17),
	(18, \'Kids Cassie\', NULL, 18),
	(19, \'Kids Leggings\', NULL, 19),
	(20, \'Kids Maxi\', NULL, 20),
	(21, \'Lola\', NULL, 21),
	(22, \'Lucy\', NULL, 22),
	(23, \'Madison\', NULL, 23),
	(24, \'Mae\', NULL, 24),
	(25, \'Mark\', NULL, 25),
	(26, \'Maxi\', NULL, 26),
	(27, \'Mommy and Me (Kids)\', NULL, 27),
	(28, \'Mommand and Me (Tween)\', NULL, 28),
	(29, \'Nicole\', NULL, 29),
	(30, \'One Size Leggings\', NULL, 30),
	(31, \'Outfit\', NULL, 31),
	(32, \'Patrick Tee\', NULL, 32),
	(33, \'Perfect Tee\', NULL, 33),
	(34, \'Randy\', NULL, 34),
	(35, \'Sloan\', NULL, 35),
	(36, \'Tall and Curvy Leggings\', NULL, 36),
	(37, \'Tween Leggings\', NULL, 37);
');
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
