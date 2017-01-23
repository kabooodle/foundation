<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'flashsalegroups' . DIRECTORY_SEPARATOR.'routes.php';

$api->get('/flashsales', [
    'as' => 'flashsales.index',
    'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesApiController::class.'@index'
]);

$api->group(['middleware' => 'jwt.auth'], function ($api) {
    $api->post('/flashsales', [
        'as' => 'flashsales.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesApiController::class . '@store'
    ]);
    $api->get('/flashsales/{flashsale}', [
        'as' => 'flashsales.show',
        'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesApiController::class . '@show'
    ]);
    $api->put('/flashsales/{flashsale}', [
        'as' => 'flashsales.update',
        'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesApiController::class . '@update'
    ]);
    $api->delete('/flashsales/{flashsale}', [
        'as' => 'flashsales.delete',
        'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesApiController::class . '@destroy'
    ]);
    $api->post('/flashsales/{flashsale}/watchers', [
        'as' => 'flashsales.watchers.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesWatchesController::class . '@store'
    ]);
    $api->delete('/flashsales/{flashsale}/watchers', [
        'as' => 'flashsales.watchers.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesWatchesController::class . '@destroy'
    ]);
});