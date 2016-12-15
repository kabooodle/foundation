<?php

Route::group(['middleware' => ['auth', 'subscribed:merchant']], function () {
    Route::resource('sales', \Kabooodle\Http\Controllers\Web\Sales\SalesController::class);
});
