<?php

use Kabooodle\Models\Queues;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queues', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('owner_id');
            $table->string('queue');
            $table->longText('payload');
            $table->integer('attempts')->default(0);
            $table->enum('status', [
                Queues::STATUS_QUEUED,
                Queues::STATUS_PROCESSING,
                Queues::STATUS_RETRYING,
                Queues::STATUS_SUCCESS,
                Queues::STATUS_FAILED
            ])->default(Queues::STATUS_QUEUED);
            $table->bigInteger('queuable_id')->nullable();
            $table->longText('queueable_type')->nullable();
            $table->timestamp('status_updated_at');
            $table->timestamps();
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
        Schema::drop('queues');
    }
}
