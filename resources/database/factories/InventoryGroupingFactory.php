<?php


$factory->define(Kabooodle\Models\InventoryGrouping::class, function (Faker\Generator $faker) {
    return [
        'uuid' => str_random(16),
        'user_id' => function () {
            return factory(\Kabooodle\Models\User::class)->create()->id;
        },
        'name' => $faker->words(random_int(1, 4), true),
        'description' => $faker->sentences(random_int(1, 3), true),
        'locked' => true,
        'price_usd' => $faker->randomNumber(),
        'barcode' => str_random(100),
        'initial_qty' => random_int(1, 5),
        'cover_photo_file_id' => function () use ($faker) {
            return factory(\Kabooodle\Models\Files::class)->create([
                'fileable_id' => $faker->randomNumber(),
                'fileable_type' => \Kabooodle\Models\InventoryGrouping::class,
            ])->id;
        },
        'date_received' => $faker->dateTime,
        'created_by' => function (array $inventory) {
            return $inventory['user_id'];
        },
        'updated_by' => function (array $inventory) {
            return $inventory['user_id'];
        },
        'deleted_by' => null,
    ];
});


$factory->defineAs(Kabooodle\Models\Inventory::class, 'unlocked', function (Faker\Generator $faker) {
    return [
        'locked' => false,
    ];
});


$factory->defineAs(Kabooodle\Models\Inventory::class, 'deleted', function (Faker\Generator $faker) {
    return [
        'deleted_by' => function (array $inventory) {
            return $inventory['user_id'];
        },
    ];
});
