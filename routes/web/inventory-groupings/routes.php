<?php

Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => 'subscribed:merchant|merchant_plus'], function(){
        Route::resource('shop.outfits', \Kabooodle\Http\Controllers\Web\Shop\InventoryGroupings\InventoryGroupingsController::class, [
            'parameters' => ['shop' => 'username'],
            'only' => ['index', 'show'],
        ]);
    });
});