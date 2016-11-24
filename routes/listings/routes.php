<?php

Route::group(['middleware' => ['auth', 'subscribed:main']], function () {

    Route::get('listings', [
        'as' => 'listings.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@index'
    ]);
});