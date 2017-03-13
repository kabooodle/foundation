<?php

$api->put('inventory/{id}', [
    'as' => 'inventory.update',
    'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@update'
]);
$api->post('inventory/archive/bulk', [
    'as' => 'inventory.archive.bulk',
    'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryArchiveApiController::class.'@bulkArchive'
]);
$api->post('inventory/activate/bulk', [
    'as' => 'inventory.activate.bulk',
    'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryArchiveApiController::class.'@bulkActivate'
]);
$api->get('inventory/archive', [
    'as' => 'inventory.archive.index',
    'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryArchiveApiController::class.'@index'
]);
$api->get('users/{username}/inventory', [
    'as' => 'inventory.index',
    'uses' =>  \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@index',
]);
$api->get('users/{username}/inventory/detailed', [
    'as' => 'inventory.detailed.index',
    'uses' =>  \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@detailed',
]);
$api->get('inventory/{inventoryid}/comments', [
    'as' => 'inventory.comments.index',
    'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryCommentsController::class.'@index'
]);

$api->group(['middleware' => 'jwt.auth'], function($api){
    $api->post('inventory/{username}/associate', [
        'as' => 'inventory.associate.store',
        'uses' =>  \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@associate',
    ]);
    $api->delete('inventory/{username}/associate/{id}', [
        'as' => 'inventory.associate.destroy',
        'uses' =>  \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@destroyAssociation',
    ]);
    $api->post('inventory/{inventoryid}/comments', [
        'as' => 'inventory.comments.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryCommentsController::class.'@store'
    ]);
    $api->delete('inventory/{inventoryid}/comments/{commentId}', [
        'as' => 'inventory.comments.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryCommentsController::class.'@destroy'
    ]);
});
