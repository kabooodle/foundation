<?php

// Get a phone object
$api->get('/phonenumbers', [
    'as' => 'phonenumbers.index',
    'uses' => \Kabooodle\Http\Controllers\Api\PhoneNumbers\PhoneNumbersApiController::class.'@index'
]);

// Create a phone object
$api->post('/phonenumbers', [
    'as' => 'phonenumbers.store',
    'uses' => \Kabooodle\Http\Controllers\Api\PhoneNumbers\PhoneNumbersApiController::class.'@store'
]);

// Create a phone object
$api->put('/phonenumbers', [
    'as' => 'phonenumbers.update',
    'uses' => \Kabooodle\Http\Controllers\Api\PhoneNumbers\PhoneNumbersApiController::class.'@update'
]);
