<?php

$api->post('/flashsales/groups', [
    'as' => 'flashsales.groups.store',
    'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesGroupsApiController::class.'@store'
]);


$api->post('/flashsales/groups/search', [
    'as' => 'flashsales.groups.search',
    'uses' => \Kabooodle\Http\Controllers\Api\Flashsales\FlashsalesGroupsApiController::class.'@query'
]);