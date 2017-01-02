<?php
$api->group(['middleware' => 'jwt.auth'], function ($api) {
    $api->get('messenger', [
        'as' => 'messenger.index',
        'uses' => \Kabooodle\Http\Controllers\Api\Messenger\MessengerApiController::class.'@index'
    ]);
    $api->post('messenger', [
        'as' => 'messenger.store',
        'middleware' => 'api.throttle',
        'limit' => 10,
        'expires' => 10,
        'uses' => \Kabooodle\Http\Controllers\Api\Messenger\MessengerApiController::class.'@store'
    ]);
    $api->get('messenger/{thread}', [
        'as' => 'messenger.show',
        'uses' => \Kabooodle\Http\Controllers\Api\Messenger\MessengerApiController::class.'@show'
    ]);
    $api->put('messenger/{thread}', [
        'as' => 'messenger.update',
        'uses' => \Kabooodle\Http\Controllers\Api\Messenger\MessengerApiController::class.'@update'
    ]);
    $api->post('messenger/{thread}/markasread', [
        'as' => 'messenger.markasread',
        'uses' => \Kabooodle\Http\Controllers\Api\Messenger\MessengerApiController::class.'@updateThreadMarkAsRead'
    ]);
});