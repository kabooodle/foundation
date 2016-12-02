<?php

    $api->post('user/{id}/followers', [
        'as' => 'user.followers.store',
        'uses' => \Kabooodle\Http\Controllers\Api\User\FollowsController::class.'@store'
    ]);
    $api->delete('user/{id}/followers', [
        'as' => 'user.followers.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\User\FollowsController::class.'@store'
    ]);
    $api->post('sales/filter', [
        'as' => 'sales.filter',
        'uses' => \Kabooodle\Http\Controllers\Api\Sales\SalesFilterController::class.'@search'
    ]);
    $api->post('shipping/filter', [
        'as' => 'shipping.filter',
        'uses' => \Kabooodle\Http\Controllers\Api\Shipping\ShippingFilterController::class.'@search'
    ]);

    $api->resource('claims', \Kabooodle\Http\Controllers\Api\Claims\ClaimsApiController::class);
    $api->post('claims/{claims}/toggle_shipping', [
        'as' => 'claims.toggle',
        'uses' => \Kabooodle\Http\Controllers\Api\Claims\ClaimsApiController::class.'@switchShippingMethod'
    ]);

    $api->resource('groups', \Kabooodle\Http\Controllers\Api\Groups\GroupsApiController::class);
    $api->resource('groups.followers', \Kabooodle\Http\Controllers\Api\Groups\GroupsFollowersApiController::class);

    $api->put('inventory/{id}', [
        'as' => 'inventory.update',
        'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@update'
    ]);
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
    $api->get('inventory/{inventoryid}/comments', [
        'as' => 'inventory.comments.index',
        'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryCommentsController::class.'@index'
    ]);
    $api->post('inventory/{inventoryid}/comments', [
        'as' => 'inventory.comments.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryCommentsController::class.'@store'
    ]);
    $api->delete('inventory/{inventoryid}/comments/{commentId}', [
        'as' => 'inventory.comments.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryCommentsController::class.'@destroy'
    ]);


    $api->post('pageviews', [
        'as' => 'inventory.pageviews.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryViewsController::class.'@store'
    ]);


    $api->post('listings/{listing}/listingitems/{listingitem}/claims', [
        'as' => 'listings.listingitems.claims.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingItemsClaimsController::class.'@store'
    ]);
    $api->post('listings/{listing}/listingitems/{listingitem}/watchers', [
        'as' => 'listings.listingitems.watchers.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\WatchesController::class.'@store'
    ]);
    $api->delete('listings/{listing}/listingitems/{listingitem}/watchers', [
        'as' => 'listings.listingitems.watchers.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\WatchesController::class.'@destroy'
    ]);