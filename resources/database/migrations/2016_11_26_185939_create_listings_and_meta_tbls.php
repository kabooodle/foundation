<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingsAndMetaTbls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\Kabooodle\Models\Listings::getTableName(), function(Blueprint $table){
            $table->bigIncrements('id');
            $table->integer('owner_id')->unsigned();
            $table->bigInteger('fb_group_node_id')->unsigned()->nullable();
            $table->integer('flashsale_id')->unsigned()->nullable();
            $table->binary('uuid');
            $table->string('name');
            $table->timestamp('scheduled_for')->nullable();
            $table->enum('type', \Kabooodle\Models\Listings::getConstantsStartsWith('TYPE'))
                ->index()
                ->default(\Kabooodle\Models\Listings::TYPE_FACEBOOK);
            $table->enum('status', \Kabooodle\Models\Listings::getConstantsStartsWith('STATUS'))
                ->index()
                ->default(\Kabooodle\Models\Listings::STATUS_SCHEDULED);
            $table->text('status_history');
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create(\Kabooodle\Models\ListingItems::getTableName(), function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('listing_id')->unsigned();
            $table->integer('owner_id')->unsigned();
            $table->bigInteger('fb_group_node_id')->unsigned()->nullable();
            $table->bigInteger('fb_album_node_id')->unsigned()->nullable();
            $table->integer('flashsale_id')->unsigned()->nullable();
            $table->integer('inventory_id')->unsigned();
            $table->binary('uuid');
            $table->tinyInteger('ignore')->default(false);
            $table->enum('type', \Kabooodle\Models\Listings::getConstantsStartsWith('TYPE'))
                ->index()
                ->default(\Kabooodle\Models\Listings::TYPE_FACEBOOK);
            $table->enum('status', \Kabooodle\Models\Listings::getConstantsStartsWith('STATUS'))
                ->index()
                ->default(\Kabooodle\Models\Listings::STATUS_SCHEDULED);
            $table->text('status_history');
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('listing_id')
                ->references('id')->on('listings')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('owner_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('inventory_id')
                ->references('id')->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        DB::update("ALTER TABLE ".\Kabooodle\Models\ListingItems::getTableName()." AUTO_INCREMENT = 118453;");
        DB::update("ALTER TABLE ".\Kabooodle\Models\Listings::getTableName()." AUTO_INCREMENT = 190345;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(\Kabooodle\Models\ListingItems::getTableName());
        Schema::drop(\Kabooodle\Models\Listings::getTableName());
    }
}
