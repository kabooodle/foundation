<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditChargeTypesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\CreditChargeTypes::getTableName(), function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->decimal('credits_equiv', 10, 2);
            $table->decimal('per_credit', 10, 2);
            $table->boolean('active')->default(1);
        });

        DB::statement('INSERT INTO `credit_charge_types` (`id`, `name`, `slug`, `description`, `amount`, `credits_equiv`, `per_credit`, `active`)
VALUES
	(1, \'$25 for $25 credits\', \'\', \'\', 25.00, 25.00, 1.00, 1),
	(2, \'$50 for $52 credits\', \'\', \'\', 50.00, 52.00, 0.96, 1),
	(3, \'$100 for $105 credits\', \'\', \'\', 100.00, 105.00, 0.95, 1),
	(4, \'$200 for $212 credits\', \'\', \'\', 200.00, 212.00, 0.94, 1),
	(5, \'$300 for $323 credits\', \'\', \'\', 300.00, 323.00, 0.93, 1),
	(6, \'$400 for $434 credits\', \'\', \'\', 400.00, 434.00, 0.92, 1);
');


        Schema::table('credit_receipts', function(Blueprint $table){
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('credit_charge_type_id')
                ->references('id')->on(\Kabooodle\Models\CreditChargeTypes::getTableName())
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
        //
    }
}
