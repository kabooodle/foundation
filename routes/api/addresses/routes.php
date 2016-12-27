<?php

$api->get('user/{id}/addresses', [
    'as' => 'user.addresses.index',
    'uses' => \Kabooodle\Http\Controllers\Api\User\AddressController::class.'@index'
]);

$api->post('user/{id}/addresses', [
    'as' => 'user.addresses.store',
    'uses' => \Kabooodle\Http\Controllers\Api\User\AddressController::class.'@store'
]);

$api->put('user/{id}/addresses/update-primary', [
    'as' => 'user.addresses.update-primary',
    'uses' => \Kabooodle\Http\Controllers\Api\User\AddressController::class.'@updatePrimary'
]);

$api->put('user/{id}/addresses/{email}', [
    'as' => 'user.addresses.update',
    'uses' => \Kabooodle\Http\Controllers\Api\User\AddressController::class.'@update'
]);

$api->delete('user/{id}/addresses/{email}', [
    'as' => 'user.addresses.destroy',
    'uses' => \Kabooodle\Http\Controllers\Api\User\AddressController::class.'@destroy'
]);
