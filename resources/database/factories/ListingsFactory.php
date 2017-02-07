<?php


$factory->define(Kabooodle\Models\Listings::class, function (Faker\Generator $faker) {
    return [
        'owner_id' => function () {
            return factory(Kabooodle\Models\User::class)->create()->id;
        },
        'fb_group_node_id' => 0,
        'flashsale_id' => function () {
            return factory(Kabooodle\Models\FlashSales::class)->create()->id;
        },
        'queue_id' => random_int(1, 10),
        'uuid' => \Ramsey\Uuid\Uuid::uuid4(),
        'name' => $faker->words(random_int(1, 3), true),
        'scheduled_for' => $faker->dateTime,
        'scheduled_until' => $faker->dateTime,
        'claimable_at' => $faker->dateTime,
        'claimable_until' => $faker->dateTime,
        'include_link_in_descr' => true,
        'type' => array_rand(Kabooodle\Models\ListingItems::TYPES),
        'status' => array_rand(Kabooodle\Models\ListingItems::STATUSES),
        'status_history' => '',
        'status_updated_at' => $faker->dateTime,
    ];
});


$factory->defineAs(Kabooodle\Models\Listings::class, 'no-link-in-desc', function (Faker\Generator $faker) {
    return [
        'include_link_in_descr' => false,
    ];
});
