<?php

Route::group(['middleware' => ['auth']], function () {
        Route::resource('shop', \Kabooodle\Http\Controllers\Web\Shop\UserShopController::class, [
            'parameters' => ['shop' => 'username'],
            'middleware' => 'subscribed:main',
            'only' => ['index','show']
        ]);
        Route::resource('shop.inventory', \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class, [
            'parameters' => ['shop' => 'username'],
            'middleware' => 'subscribed:main',
        ]);
        Route::get('shop{username}/inventory/query', [
            'as' => 'shop.inventory.query',
            'middleware' => 'subscribed:main',
            'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class.'@queryIndex'
        ]);
        Route::resource('shop.claims', \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryClaimsController::class, [
            'only' => ['index', 'show', 'update', 'destroy'],
            'parameters' => ['shop' => 'username'],
            'middleware' => 'subscribed:main',
        ]);
        Route::post('shop/{username}/inventory/{inventory}/claim', [
            'as' => 'shop.inventory.claim',
            'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class.'@claim'
        ]);
});