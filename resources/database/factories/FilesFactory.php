<?php


$factory->define(Kabooodle\Models\Files::class, function (Faker\Generator $faker) {
    return [
        'fileable_id' => function () {
            return factory(\Kabooodle\Models\Inventory::class)->create()->id;
        },
        'fileable_type' => function (array $file) {
            return Kabooodle\Models\Inventory::find($file['fileable_id']);
        },
        'bucket_name' => $faker->word,
        'location' => $faker->imageUrl(),
        'key' => str_random(50),
        'sort_order' => random_int(0, 4),
    ];
});
