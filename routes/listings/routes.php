<?php

Route::group(['middleware' => ['auth', 'subscribed:main']], function () {

    Route::get('listings', [
        'as' => 'listings.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@index'
    ]);
    Route::get('listings/{listing}', [
        'as' => 'listings.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@show'
    ]);
    Route::get('listings/{listing}/groups/{group}', [
        'as' => 'listings.group.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@detailed'
    ]);
});