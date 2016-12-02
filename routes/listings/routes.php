<?php

Route::get('listingitems/{listingItem}', [
    'as' => 'listingitems.show',
    'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingItemsController::class.'@show'
]);

Route::group(['middleware' => ['auth', 'subscribed:main']], function () {
    Route::get('listings', [
        'as' => 'listings.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@index'
    ]);
    Route::get('listings/{listing}', [
        'as' => 'listings.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@show'
    ]);
    Route::get('listings/{listing}/{group}/{groupid}', [
        'as' => 'listings.group.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@detailed'
    ]);
});