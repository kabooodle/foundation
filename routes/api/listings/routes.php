<?php

$api->post('listings/{listing}/listingitems/{listingitem}/guest-claim', [
    'as' => 'listings.listingitems.claims.guest-store',
    'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingItemsClaimsController::class.'@guestStore'
]);

$api->get('listings/{listing}', [
    'as' => 'listings.show',
    'uses' => \Kabooodle\Http\Controllers\Api\Listings\ListingsApiController::class.'@show'
]);

$api->group(['middleware' => 'jwt.auth'], function ($api) {
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
});
