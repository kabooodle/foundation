<?php

Route::group(['middleware' => ['auth', 'subscribed:merchant_plus']], function () {
    Route::resource('shipping', \Kabooodle\Http\Controllers\Web\Shipping\ShippingOrderController::class);
    Route::resource('shipping.transactions', \Kabooodle\Http\Controllers\Web\Shipping\ShippingTransactionController::class);
    Route::get('shipping/{shipping}/transactions/{transaction}/label', [
        'as' => 'shipping.transactions.label.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Shipping\ShippingTransactionController::class.'@label'
    ]);
});
