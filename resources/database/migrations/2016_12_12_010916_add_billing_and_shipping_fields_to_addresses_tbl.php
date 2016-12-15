<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillingAndShippingFieldsToAddressesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `addresses` MODIFY COLUMN `type` ENUM('billing','ship_to','ship_from') DEFAULT 'ship_from'");
        Schema::table('addresses', function (Blueprint $table) {
            $table->boolean('primary')->default(false)->after('type');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `addresses` MODIFY COLUMN `type` ENUM('ship_to','ship_from') DEFAULT 'ship_from'");
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('primary');
            $table->dropColumn('deleted_at');
        });
    }
}
