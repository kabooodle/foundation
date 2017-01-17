<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFlashsaleGroupsPivotAddFlashsaleIdCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('flashsales_admins');
        Schema::dropIfExists('flashsales_sellers');

        DB::statement('ALTER TABLE flashsales MODIFY COLUMN id BIGINT unsigned auto_increment;');

        Schema::create('flashsales_sellers_groups', function(Blueprint $table){
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('flashsale_id')->unsigned();
            $table->bigInteger('flashsale_group_id')->unsigned();
            $table->timestamp('time_slot')->nullable();
            $table->timestamps();

            $table->foreign('flashsale_id', 'fk_fs')
                ->references('id')->on('flashsales')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('flashsale_group_id', 'fk_fg')
                ->references('id')->on('flashsales_groups')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flashsales_sellers_groups');
    }
}
