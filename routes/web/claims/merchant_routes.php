<?php

Route::group(['middleware' => ['auth', 'subscribed:merchant'], 'prefix' => 'merchant'], function () {
    Route::resource('claims', \Kabooodle\Http\Controllers\Web\Claims\MerchantClaimsController::class, [
        'only' => ['index', 'show', 'update', 'destroy']
    ]);
});