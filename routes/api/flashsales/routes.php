<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'flashsalegroups' . DIRECTORY_SEPARATOR.'routes.php';


$api->post('/flashsales', [
    'as' => 'flashsales.store',
    'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesApiController::class.'@store'
]);