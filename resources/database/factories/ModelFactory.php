<?php


$factory->define(Kabooodle\Models\User::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->randomNumber(),
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});


$factory->define(Kabooodle\Models\Comments::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->randomNumber(),
        'uuid' => \Ramsey\Uuid\Uuid::uuid4(),
        'user_id' => 1,
        'commentable_id' => 1,
        'commentable_type' => '',
        'text' => 'test',
        'text_raw' => 'test'
    ];
});


$factory->defineAs(\Kabooodle\Models\CreditTransactions::class, 'CreditTransactionsDebit', function (Faker\Generator $faker) {
    return [
        'user_id' => factory(Kabooodle\Models\User::class)->make()->id,
        'transactable_type' => '',
        'transactable_id' => 0,
        'amount' => -50,
        'transaction_amount' => -50,
        'previous_balance_of' => 0,
        'incr' => \Kabooodle\Models\CreditTransactions::INCR_DEBIT,
        'type' => \Kabooodle\Models\CreditTransactions::TYPE_DEBIT
    ];
});

$factory->defineAs(\Kabooodle\Models\CreditTransactions::class, 'CreditTransactionsCredit', function (Faker\Generator $faker) {
    return [
        'user_id' => factory(Kabooodle\Models\User::class)->make()->id,
        'transactable_type' => 'Kabooodle\Models\CreditReceipts',
        'transactable_id' => 1,
        'amount' => 100,
        'transaction_amount' => 100,
        'previous_balance_of' => 0,
        'incr' => \Kabooodle\Models\CreditTransactions::INCR_CREDIT,
        'type' => \Kabooodle\Models\CreditTransactions::TYPE_CREDIT
    ];
});
