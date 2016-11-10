<?php

    $api->post('shipping/filter', [
        'as' => 'shipping.filter',
        'uses' => \Kabooodle\Http\Controllers\Api\Shipping\ShippingFilterController::class.'@search'
    ]);
    $api->resource('claims', \Kabooodle\Http\Controllers\Api\Claims\ClaimsApiController::class);
    $api->resource('groups', \Kabooodle\Http\Controllers\Api\Groups\GroupsApiController::class);
    $api->resource('groups.followers', \Kabooodle\Http\Controllers\Api\Groups\GroupsFollowersApiController::class);
    $api->get('inventory/{username}', [
        'as' => 'inventory.index',
        'uses' =>  \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@index',
    ]);
    $api->post('inventory/{username}/associate', [
        'as' => 'inventory.associate.store',
        'uses' =>  \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@associate',
    ]);
    $api->delete('inventory/{username}/associate/{id}', [
        'as' => 'inventory.associate.destroy',
        'uses' =>  \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@destroyAssociation',
    ]);
