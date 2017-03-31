<?php
$api->group(['middleware' => 'jwt.auth'], function ($api) {
    $api->post('users/search', [
        'as' => 'users.search',
        'uses' => \Kabooodle\Http\Controllers\Api\User\QueryUser::class . '@query'
    ]);
});

$api->get('users/{username}/listings', [
    'as' => 'users.listings.index',
    'uses' => \Kabooodle\Http\Controllers\Api\User\ListingsController::class . '@index'
]);
$api->get('users/{username}/listings/{uuid}', [
    'as' => 'users.listings.show',
    'uses' => \Kabooodle\Http\Controllers\Api\User\ListingsController::class . '@show'
]);

$api->get('users/instant/search/{username}', [
    'as' => 'users.instant.search',
    'uses' => \Kabooodle\Http\Controllers\Api\User\QueryUser::class . '@searchUser'
]);
