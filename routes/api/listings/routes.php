<?php

$api->post('listings/{listing}/listingitems/{listingitem}/guest-claim', [
    'as' => 'listings.listingitems.claims.guest-store',
    'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingItemsClaimsController::class.'@guestStore'
]);

$api->get('listings/{listing}', [
    'as' => 'listings.show',
    'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingsApiController::class.'@show'
]);

$api->post('listings/shoppablelink', [
    'as' => 'listings.shoppablelink.store',
    'uses' => \Kabooodle\Http\Controllers\Api\Listings\ShoppablelinkApiController::class.'@store'
]);

$api->group(['middleware' => 'jwt.auth'], function ($api) {
    $api->delete('listings/{listing}', [
        'as' => 'listings.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingsApiController::class.'@destroy'
    ]);
    $api->delete('listings/{listing}/facebook', [
        'as' => 'listings.facebook.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingsApiController::class.'@destroyFromFacebook'
    ]);
    $api->post('listings', [
        'as' => 'listings.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingsApiController::class.'@store'
    ]);
    $api->post('listings/{listing}/listingitems/{listingitem}/claims', [
        'as' => 'listings.listingitems.claims.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingItemsClaimsController::class . '@store'
    ]);
    $api->post('listings/{listing}/listingitems/{listingitem}/watchers', [
        'as' => 'listings.listingitems.watchers.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\WatchesController::class . '@store'
    ]);
    $api->delete('listings/{listing}/listingitems/{listingitem}/watchers', [
        'as' => 'listings.listingitems.watchers.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\WatchesController::class . '@destroy'
    ]);
    $api->delete('listings/{listing}/listingsitems/{listingitem}', [
        'as' => 'listings.listingitems.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingItemsApiController::class . '@destroy'
    ]);
    $api->delete('listings/{listing}/listingsitems/{listingitem}/facebook', [
        'as' => 'listings.listingitems.facebook.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingItemsApiController::class . '@destroyFromFacebook'
    ]);
});
