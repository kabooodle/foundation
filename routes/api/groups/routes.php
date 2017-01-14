<?php

$api->post('listings/{listing}/listingitems/{listingitem}/guest-claim', [
    'as' => 'listings.listingitems.claims.guest-store',
    'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingItemsClaimsController::class.'@guestStore'
]);

$api->group(['middleware' => 'jwt.auth'], function ($api) {
    $api->post('user/{id}/followers', [
        'as' => 'user.followers.store',
        'uses' => \Kabooodle\Http\Controllers\Api\User\FollowsController::class.'@store'
    ]);
    $api->delete('user/{id}/followers', [
        'as' => 'user.followers.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\User\FollowsController::class.'@destroy'
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
});
