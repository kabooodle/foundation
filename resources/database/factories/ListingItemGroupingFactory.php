<?php


$factory->define(Kabooodle\Models\ListingItemGrouping::class, function (Faker\Generator $faker) {
    return [
        'listing_id' => function () {
            return factory(Kabooodle\Models\Listings::class)->create()->id;
        },
        'owner_id' => function () {
            return factory(Kabooodle\Models\User::class)->create()->id;
        },
        'fb_group_node_id' => 0,
        'fb_album_node_id' => 0,
        'fb_response_object_id' => 0,
        'flashsale_id' => function () {
            return factory(Kabooodle\Models\FlashSales::class)->create()->id;
        },
        'subclass_name' => Kabooodle\Models\ListingItemGrouping::class,
        'listable_id' => function () {
            return factory(Kabooodle\Models\InventoryGrouping::class)->create()->id;
        },
        'queue_id' => random_int(1, 10),
        'uuid' => \Ramsey\Uuid\Uuid::uuid4(),
        'item_message' => $faker->words(random_int(1, 5), true),
        'fb_response' => str_random(25),
        'ignore' => false,
        'type' => array_rand(Kabooodle\Models\ListingItems::TYPES),
        'status' => array_rand(Kabooodle\Models\ListingItems::STATUSES),
        'status_history' => '',
        'status_updated_at' => $faker->dateTime,
        'make_available_at' => $faker->dateTime,
    ];
});
