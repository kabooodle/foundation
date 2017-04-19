<?php

Route::group(['as' => 'merchant.', 'middleware' => ['auth', 'subscribed:merchant'], 'prefix' => 'merchant'], function () {
    Route::resource('claims', \Kabooodle\Http\Controllers\Web\Claims\MerchantClaimsController::class, [
        'only' => ['index', 'show', 'update', 'destroy']
    ]);
});