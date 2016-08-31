<?php

Route::group(['middleware' => 'auth'], function () {
        Route::resource('shop', \Kabooodle\Http\Controllers\Web\Shop\UserShopController::class, [
            'parameters' => ['shop' => 'username'],
            'only' => ['index','show']
        ]);
        Route::resource('shop.inventory', \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryController::class, [
            'parameters' => ['shop' => 'username']
        ]);
        Route::get('shop/{username}/associate', [
            'as' => 'shop.inventory.associate.create',
            'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\ManageInventoryForSalesController::class.'@create'
        ]);
});