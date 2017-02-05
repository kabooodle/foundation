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
            $table->bigInteger('owner_id')->unsigned();
            $table->bigInteger('fb_group_node_id')->unsigned()->nullable();
            $table->integer('flashsale_id')->unsigned()->nullable();
            $table->binary('uuid');
            $table->string('name');
            $table->timestamp('scheduled_for')->nullable();
            $table->enum('type', \Kabooodle\Models\Listings::TYPES)
                ->index()
                ->default(\Kabooodle\Models\Listings::TYPE_FACEBOOK);
            $table->enum('status', \Kabooodle\Models\Listings::STATUSES)
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
            $table->bigInteger('owner_id')->unsigned();
            $table->bigInteger('fb_group_node_id')->unsigned()->nullable();
            $table->bigInteger('fb_album_node_id')->unsigned()->nullable();
            $table->integer('flashsale_id')->unsigned()->nullable();
            $table->bigInteger('inventory_id')->unsigned();
            $table->binary('uuid');
            $table->tinyInteger('ignore')->default(false);
            $table->enum('type', \Kabooodle\Models\Listings::TYPES)
                ->index()
                ->default(\Kabooodle\Models\Listings::TYPE_FACEBOOK);
            $table->enum('status', \Kabooodle\Models\Listings::STATUSES)
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

        DB::update("ALTER TABLE ".\Kabooodle\Models\ListingItems::getTableName()." AUTO_INCREMENT = 9818453;");
        DB::update("ALTER TABLE ".\Kabooodle\Models\Listings::getTableName()." AUTO_INCREMENT = 10990345;");

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::drop('facebook_items');
        Schema::drop('flashsale_items');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
