<?php


$factory->define(Kabooodle\Models\ListingItems::class, function (Faker\Generator $faker) {
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
        'subclass_name' => function () {
            return \Kabooodle\Models\ListingItems::SUB_TYPES[array_rand(\Kabooodle\Models\ListingItems::SUB_TYPES)];
        },
        'listable_id' => function (array $listingItem) {
            if ($listingItem['subclass_name'] == \Kabooodle\Models\ListingItemSingle::class) {
                return factory(\Kabooodle\Models\Inventory::class)->create()->id;
            } else if ($listingItem['subclass_name'] == \Kabooodle\Models\ListingItemGrouping::class) {
                return factory(\Kabooodle\Models\InventoryGrouping::class)->create()->id;
            }
            return null;
        },
        'queue_id' => random_int(1, 10),
        'uuid' => \Ramsey\Uuid\Uuid::uuid4(),
        'item_message' => $faker->words(random_int(1, 5), true),
        'fb_response' => str_random(25),
        'ignore' => false,
        'type' => Kabooodle\Models\ListingItems::TYPES[array_rand(Kabooodle\Models\ListingItems::TYPES)],
        'status' => Kabooodle\Models\ListingItems::STATUSES[array_rand(Kabooodle\Models\ListingItems::STATUSES)],
        'status_history' => '',
        'status_updated_at' => $faker->dateTime,
        'make_available_at' => $faker->dateTime,
    ];
});


$factory->defineAs(Kabooodle\Models\ListingItems::class, 'single', function (Faker\Generator $faker) {
    return [
        'subclass_name' => Kabooodle\Models\ListingItemSingle::class,
        'listable_id' => function () {
            return factory(Kabooodle\Models\Inventory::class)->create()->id;
        },
    ];
});


$factory->defineAs(Kabooodle\Models\ListingItems::class, 'grouping', function (Faker\Generator $faker) {
    return [
        'subclass_name' => Kabooodle\Models\ListingItemGrouping::class,
        'listable_id' => function () {
            return factory(Kabooodle\Models\InventoryGrouping::class)->create()->id;
        },
    ];
});
