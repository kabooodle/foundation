<?php


$factory->define(Kabooodle\Models\Inventory::class, function (Faker\Generator $faker) {
    return [
        'uuid' => str_random(16),
        'inventory_type_styles_id' => function () {
            return \Kabooodle\Models\InventoryTypeStyles::all()->random()->id;
        },
        'inventory_sizes_id' => function (array $inventory) {
            return Kabooodle\Models\InventoryTypeStyles::find($inventory['inventory_type_styles_id'])->sizes->random()->id;
        },
        'inventory_type_id' => function () {
            return \Kabooodle\Models\InventoryType::whereSlug('lularoe')->first()->id;
        },
        'user_id' => function () {
            return factory(\Kabooodle\Models\User::class)->create()->id;
        },
        'name' => function (array $inventory) {
            return Kabooodle\Models\InventoryTypeStyles::find($inventory['inventory_type_styles_id'])->name;
        },
        'description' => $faker->sentences(random_int(1, 3), true),
        'price_usd' => function (array $inventory) {
            return Kabooodle\Models\InventoryTypeStyles::find($inventory['inventory_type_styles_id'])->suggested_price_usd;
        },
        'wholesale_price_usd' => function (array $inventory) {
            return Kabooodle\Models\InventoryTypeStyles::find($inventory['inventory_type_styles_id'])->wholesale_price_usd;
        },
        'barcode' => str_random(100),
        'initial_qty' => random_int(1, 5),
        'cover_photo_file_id' => function (array $inventory) use ($faker) {
            return factory(\Kabooodle\Models\Files::class)->create([
                'fileable_id' => $faker->randomNumber(),
                'fileable_type' => \Kabooodle\Models\Inventory::class,
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


$factory->defineAs(Kabooodle\Models\Inventory::class, 'deleted', function (Faker\Generator $faker) {
    return [
        'deleted_by' => function (array $inventory) {
            return $inventory['user_id'];
        },
    ];
});
