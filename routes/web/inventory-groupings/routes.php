<?php

Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => 'subscribed:merchant|merchant_plus'], function(){
        Route::resource('shop.outfits', \Kabooodle\Http\Controllers\Web\InventoryGroupings\InventoryGroupingsController::class, [
            'parameters' => ['shop' => 'username'],
            'except' => ['store', 'update', 'destroy'],
        ]);
        Route::get('shop/{username}/outfits-simple', [
            'as' =>'shop.outfits.overview.index',
            'uses' => \Kabooodle\Http\Controllers\Web\InventoryGroupings\InventoryGroupingsController::class.'@simple'
        ]);
    });
});