<?php


$factory->define(Kabooodle\Models\Comments::class, function (Faker\Generator $faker) {
    return [
        'uuid' => \Ramsey\Uuid\Uuid::uuid4(),
        'user_id' => 1,
        'commentable_id' => 1,
        'commentable_type' => '',
        'text' => 'test',
        'text_raw' => 'test'
    ];
});
