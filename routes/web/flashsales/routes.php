<?php

Route::resource('flashsales', \Kabooodle\Http\Controllers\Web\FlashSales\FlashSalesController::class);

Route::group(['middleware' => 'auth'], function () {
        Route::resource('flashsales.shop', \Kabooodle\Http\Controllers\Web\FlashSales\SellersInventoryController::class, ['only' => ['show', 'index','edit']]);
        Route::post('flashsales/{flashsales}/shop/{shop}/claim', [
            'as' => 'flashsales.shop.claim',
            'uses' => \Kabooodle\Http\Controllers\Web\FlashSales\SellersInventoryController::class.'@claim'
        ]);
        Route::post('flashsales/{flashsales}/shop/{shop}/comments', [
            'as' => 'flashsales.shop.comments.store',
            'uses' => \Kabooodle\Http\Controllers\Web\FlashSales\SellersInventoryController::class.'@storeComment'
        ]);
        Route::delete('flashsales/{flashsales}/shop/{shop}/comments/{comment}', [
            'as' => 'flashsales.shop.comments.destroy',
            'uses' => \Kabooodle\Http\Controllers\Web\FlashSales\SellersInventoryController::class.'@deleteComment'
        ]);
});

Route::resource('flashsales.invitations', \Kabooodle\Http\Controllers\Web\FlashSales\InvitationsController::class);