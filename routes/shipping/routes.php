<?php

Route::group(['middleware' => 'auth'], function () {
    Route::resource('shipping', \Kabooodle\Http\Controllers\Web\Shipping\ShippingOrderController::class);
    Route::resource('shipping.labels', \Kabooodle\Http\Controllers\Web\Shipping\ShippingLabelController::class);
});
