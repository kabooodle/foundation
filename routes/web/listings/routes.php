<?php


Route::get('listing/{listing}', [
    'as' =>'listings.show',
    'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@show'
]);
// Shortened url, possible for sharing?
Route::get('l/{listing}', [
    'as' =>'listings.shorthand',
    'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@shorthand'
]);

Route::get('listingitems/{listingItem}', [
    'as' => 'listingitems.show',
    'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingItemsController::class.'@show'
]);

require_once('merchant_routes.php');