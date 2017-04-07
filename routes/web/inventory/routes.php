<?php

Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => 'subscribed:merchant|merchant_plus'], function(){
        Route::resource('shop', \Kabooodle\Http\Controllers\Web\Shop\UserShopController::class, [
            'parameters' => ['shop' => 'username'],
            'only' => ['index','show']
        ]);
        Route::resource('shop.inventory', \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class, [
            'parameters' => ['shop' => 'username'],
        ]);

        Route::get('inventory/postables', [
            'as' => 'inventory.postables',
            'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class.'@postables'
        ]);
        Route::get('shop/{username}/inventory/query', [
            'as' => 'shop.inventory.query',
            'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class.'@queryIndex'
        ]);
        Route::get('shop/{username}/inventory-simple', [
            'as' =>'shop.inventory.overview.show',
            'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class.'@simple'
        ]);
        Route::get('shop/{username}/inventory-archive', [
            'as' =>'shop.inventory.archive.index',
            'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryArchiveController::class.'@index'
        ]);
    });

    Route::post('shop/{username}/inventory/{inventory}/claim', [
        'as' => 'shop.inventory.claim',
        'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class.'@claim'
    ]);

    Route::post('shop/{username}/inventory/{inventory}/guest-claim', [
        'as' => 'guest.claim',
        'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class.'@guestClaim'
    ]);
});