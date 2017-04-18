<?php

Route::group(['as' => 'merchant.', 'middleware' => ['auth', 'subscribed:merchant'], 'prefix' => 'merchant'], function () {
    Route::resource('sales', \Kabooodle\Http\Controllers\Web\Sales\SalesController::class);
});
