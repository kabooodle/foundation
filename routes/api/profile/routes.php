<?php

$api->post('subscription/trial', [
    'as' => 'subscription.trial.store',
    'uses' => \Kabooodle\Http\Controllers\Api\Profile\ProfileApiController::class.'@subscribeToTrial'
]);

$api->post('user/{id}/emails', [
    'as' => 'user.emails.store',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@store'
]);

$api->put('user/{id}/emails/{email}', [
    'as' => 'user.emails.update',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@update'
]);

$api->get('user/{id}/emails/{email}/resend-verification', [
    'as' => 'user.emails.resend-verification',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@resendVerification'
]);

$api->delete('user/{id}/emails/{email}', [
    'as' => 'user.emails.destroy',
    'uses' => \Kabooodle\Http\Controllers\Api\User\EmailController::class.'@destroy'
]);
