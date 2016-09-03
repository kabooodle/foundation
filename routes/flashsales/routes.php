<?php

Route::group(['middleware' => 'auth'], function () {
    Route::resource('flashsales', \Kabooodle\Http\Controllers\Web\FlashSales\FlashSalesController::class);
    Route::resource('flashsales.shop', \Kabooodle\Http\Controllers\Web\FlashSales\SellersInventoryController::class, ['only' => ['show', 'index','edit']]);
    Route::post('flashsales/{flashsales}/shop/{shop}/claim', [
        'as' => 'flashsales.shop.claim',
        'uses' => \Kabooodle\Http\Controllers\Web\FlashSales\SellersInventoryController::class.'@claim'
    ]);
});

Route::resource('flashsales.invitations', \Kabooodle\Http\Controllers\Web\FlashSales\InvitationsController::class);