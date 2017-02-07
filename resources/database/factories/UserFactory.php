<?php


$factory->define(Kabooodle\Models\User::class, function (Faker\Generator $faker) {
    return [
        'public_hash' => str_random(10),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'state' => 'CA',
        'city' => $faker->city,
        'username' => $faker->userName,
        'password' => bcrypt(str_random(10)),
        'activated' => false,
        'guest' => false,
        'timezone' => $faker->timezone,
        'remember_token' => str_random(10),
    ];
});


$factory->defineAs(Kabooodle\Models\User::class, 'guest', function (Faker\Generator $faker) {
    return [
        'password' => false,
        'guest' => true,
    ];
});
