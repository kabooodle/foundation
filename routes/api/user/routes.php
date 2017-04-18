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

$api->get('users/{username}/claims', [
    'as' => 'users.claims.index',
    'uses' => \Kabooodle\Http\Controllers\Api\User\ClaimsController::class . '@index'
]);
$api->get('users/{username}/claims/{uuid}', [
    'as' => 'users.claims.show',
    'uses' => \Kabooodle\Http\Controllers\Api\User\ClaimsController::class . '@show'
]);
$api->post('users/{username}/claims/{uuid}/cancel', [
    'as' => 'users.claims.cancel',
    'uses' => \Kabooodle\Http\Controllers\Api\User\ClaimsController::class . '@cancel'
]);
