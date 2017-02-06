<?php


$factory->define(Kabooodle\Models\Email::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return factory(\Kabooodle\Models\User::class)->create()->id;
        },
        'address' => $faker->safeEmail,
        'primary' => false,
        'verified' => false,
        'token' => \Ramsey\Uuid\Uuid::uuid4(),
    ];
});


$factory->defineAs(Kabooodle\Models\Email::class, 'primary', function (Faker\Generator $faker) {
    return [
        'primary' => true,
    ];
});


$factory->defineAs(Kabooodle\Models\Email::class, 'verified', function (Faker\Generator $faker) {
    return [
        'verified' => true,
        'token' => null,
    ];
});


$factory->defineAs(Kabooodle\Models\Email::class, 'primary-verified', function (Faker\Generator $faker) {
    return [
        'primary' => true,
        'verified' => true,
        'token' => null,
    ];
});
