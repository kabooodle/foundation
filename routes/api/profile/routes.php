<?php

$api->post('subscription/trial', [
    'as' => 'subscription.trial.store',
    'uses' => \Kabooodle\Http\Controllers\Api\Profile\ProfileApiController::class.'@subscribeToTrial'
]);

$api->put('user/{id}/shipping-profile', [
    'as' => 'user.shipping-profile.update',
    'uses' => \Kabooodle\Http\Controllers\Api\Profile\ProfileApiController::class.'@updateShippingProfile'
]);
