<?php


Route::post(
    '__captainHook/shtriwpe',
    \Kabooodle\Http\Controllers\Web\Webhooks\StripeWebhooksController::class . '@handleWebhook'
);
Route::post(
    '__captainHook/sheepoo',
    \Kabooodle\Http\Controllers\Web\Webhooks\ShippoWebhooksController::class . '@handleWebhook'
);
Route::group(['middleware' => ['auth']], function () {
    Route::post('/__captainHook/puuusher', [
        'as' => 'webhooks.pusher',
        'uses' => \Kabooodle\Http\Controllers\Web\Webhooks\PusherWebhookController::class.'@handleWebhook'
    ]);
});