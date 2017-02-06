<?php

$api->get('{username}/inventory-groupings', [
    'as' => 'inventory-groupings.index',
    'uses' =>  \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@index',
]);
$api->get('{username}/inventory-groupings/{groupingId}', [
    'as' => 'inventory-groupings.show',
    'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@show'
]);
$api->get('{username}/inventory-groupings/{groupingId}/comments', [
    'as' => 'inventory-groupings.comments.index',
    'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsCommentsController::class.'@index'
]);

$api->group(['middleware' => 'jwt.auth'], function($api) {
    // Inventory Groupings
    $api->post('{username}/inventory-groupings', [
        'as' => 'inventory-groupings.store',
        'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@store'
    ]);
    $api->put('{username}/inventory-groupings/{id}', [
        'as' => 'inventory-groupings.update',
        'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@update'
    ]);
    $api->delete('{username}/inventory-groupings/{id}', [
        'as' => 'inventory-groupings.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@destroy'
    ]);

    // Comments
    $api->post('{username}/inventory-groupings/{groupingId}/comments', [
        'as' => 'inventory-groupings.comments.store',
        'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsCommentsController::class.'@store'
    ]);
    $api->put('{username}/inventory-groupings/{groupingId}/comments/{commentId}', [
        'as' => 'inventory-groupings.comments.update',
        'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsCommentsController::class.'@update'
    ]);
    $api->delete('{username}/inventory-groupings/{groupingId}/comments/{commentId}', [
        'as' => 'inventory-groupings.comments.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsCommentsController::class.'@destroy'
    ]);

    // Associations
    $api->post('{username}/inventory-groupings/{groupingId}/associate', [
        'as' => 'inventory-groupings.associate.store',
        'uses' =>  \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@associate',
    ]);
    $api->delete('{username}/inventory-groupings/{groupingId}/associate/{id}', [
        'as' => 'inventory-groupings.associate.destroy',
        'uses' =>  \Kabooodle\Http\Controllers\Api\InventoryGroupings\InventoryGroupingsController::class.'@destroyAssociation',
    ]);
});
