<?php

Route::group(['middleware' => 'auth'], function () {
        Route::resource('shop', \Kabooodle\Http\Controllers\Web\Shop\UserShopController::class, [
            'parameters' => ['shop' => 'username'],
            'only' => ['index','show']
        ]);
        Route::resource('shop.inventory', \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class, [
            'parameters' => ['shop' => 'username']
        ]);
        Route::get('shop{username}/inventory/query', [
            'as' => 'shop.inventory.query',
            'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class.'@queryIndex'
        ]);
        Route::post('shop/{username}/inventory/{inventory}/claim', [
            'as' => 'shop.inventory.claim',
            'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class.'@claim'
        ]);
        Route::resource('shop.claims', \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryClaimsController::class, [
            'only' => ['index', 'show', 'update', 'destroy'],
            'parameters' => ['shop' => 'username']
        ]);
});