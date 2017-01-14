<?php

$api->get('user/{id}/emails', [
    'as' => 'user.emails.index',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@index'
]);

$api->post('user/{id}/emails', [
    'as' => 'user.emails.store',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@store'
]);

$api->put('user/{id}/emails/update-primary', [
    'as' => 'user.emails.update-primary',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@updatePrimary'
]);

$api->put('user/{id}/emails/resend-verification', [
    'as' => 'user.emails.resend-verification',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@resendVerification'
]);

$api->put('user/{id}/emails/{email}', [
    'as' => 'user.emails.update',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@update'
]);

$api->delete('user/{id}/emails/{email}', [
    'as' => 'user.emails.destroy',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@destroy'
]);
