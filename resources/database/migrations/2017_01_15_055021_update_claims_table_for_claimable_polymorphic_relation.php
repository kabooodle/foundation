<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClaimsTableForClaimablePolymorphicRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign('claims_inventory_id_foreign');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->string('claimable_type')->after('uuid');
        });

        DB::statement('ALTER TABLE `claims` CHANGE COLUMN `inventory_id` `claimable_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('UPDATE `claims` SET `claimable_type` = "Kabooodle\\Models\\Inventory"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `claim` CHANGE COLUMN `claimable_id` `inventory_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('claim', function (Blueprint $table) {
            $table->dropColumn('claimable_type');

            $table->foreign('inventory_id')
                ->references('id')->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
}
