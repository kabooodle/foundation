<?php

Route::group(['middleware' => ['auth', 'subscribed:main']], function () {
    Route::resource('shipping', \Kabooodle\Http\Controllers\Web\Shipping\ShippingOrderController::class);
    Route::resource('shipping.labels', \Kabooodle\Http\Controllers\Web\Shipping\ShippingLabelController::class);
});
