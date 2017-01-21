<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'flashsalegroups' . DIRECTORY_SEPARATOR.'routes.php';


$api->get('/flashsales', [
    'as' => 'flashsales.index',
    'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesApiController::class.'@index'
]);
$api->post('/flashsales', [
    'as' => 'flashsales.store',
    'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesApiController::class.'@store'
]);
$api->post('/flashsales/{flashsale}/watchers', [
    'as' => 'flashsales.watchers.store',
    'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesWatchesController::class.'@store'
]);
$api->delete('/flashsales/{flashsale}/watchers', [
    'as' => 'flashsales.watchers.destroy',
    'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesWatchesController::class.'@destroy'
]);