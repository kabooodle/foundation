<?php

Route::group(['middleware' => ['auth', 'subscribed:main']], function () {
    Route::resource('sales', \Kabooodle\Http\Controllers\Web\Sales\SalesController::class);
});
