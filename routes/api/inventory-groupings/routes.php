<?php

$api->put('inventory-grouping/{id}', [
    'as' => 'inventory-grouping.update',
    'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@update'
]);
$api->get('{username}/inventory-grouping', [
    'as' => 'inventory-grouping.index',
    'uses' =>  \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@index',
]);
$api->get('inventory-grouping/{inventoryGroupingId}/comments', [
    'as' => 'inventory-grouping.comments.index',
    'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsCommentsController::class.'@index'
]);

$api->group(['middleware' => 'jwt.auth'], function($api){
    $api->post('inventory-grouping/{username}/associate', [
        'as' => 'inventory-grouping.associate.store',
        'uses' =>  \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@associate',
    ]);
    $api->delete('inventory-grouping/{username}/associate/{id}', [
        'as' => 'inventory-grouping.associate.destroy',
        'uses' =>  \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@destroyAssociation',
    ]);
    $api->post('inventory-grouping/{inventoryGroupingId}/comments', [
        'as' => 'inventory-grouping.comments.store',
        'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsCommentsController::class.'@store'
    ]);
    $api->delete('inventory-grouping/{inventoryGroupingId}/comments/{commentId}', [
        'as' => 'inventory.comments.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsCommentsController::class.'@destroy'
    ]);
});
