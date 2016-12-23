<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kabooodle\Models\CreditChargeTypes;

class UpdateCreditChargeTypesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CreditChargeTypes::truncate();

        DB::statement('INSERT INTO `credit_charge_types` (`id`, `name`, `slug`, `description`, `amount`, `credits_equiv`, `per_credit`, `active`)
        VALUES
            (1, \'$25 for $25 credits\', \'\', \'\', 25.00, 25.00, 1.00, 1),
            (2, \'$50 for $50 credits\', \'\', \'\', 50.00, 50.00, 1.00, 1),
            (3, \'$100 for $100 credits\', \'\', \'\', 100.00, 100.00, 1.00, 1),
            (4, \'$200 for $200 credits\', \'\', \'\', 200.00, 200.00, 1.00, 1),
            (5, \'$300 for $300 credits\', \'\', \'\', 300.00, 300.00, 1.00, 1),
            (6, \'$400 for $400 credits\', \'\', \'\', 400.00, 400.00, 1.00, 1);
        ');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
