<?php

Route::group(['middleware' => 'auth'], function () {
    Route::get('/profile', [
        'as' => 'profile.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@index'
    ]);
    Route::get('/profile/addresses', [
        'as' => 'profile.addresses.edit',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@getAddresses'
    ]);
    Route::post('/profile/addresses', [
        'as' => 'profile.addresses.update',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@postAddresses'
    ]);
    Route::get('/profile/socialprofiles', [
        'as' => 'profile.social.edit',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@getSocial'
    ]);

    Route::get('/profile/subscription', [
        'as' => 'profile.subscription.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionsController::class.'@index'
    ]);
    Route::post('/profile/subscription', [
        'as' => 'profile.subscription.store',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionsController::class.'@store'
    ]);
    Route::get('/profile/subscription/invoices', [
        'as' => 'profile.subscription.invoices.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionInvoicesController::class.'@index'
    ]);
    Route::get('/profile/creditcard', [
        'as' => 'profile.creditcard.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileCreditCardController::class.'@index'
    ]);
    Route::post('/profile/creditcard', [
        'as' => 'profile.creditcard.store',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileCreditCardController::class.'@store'
    ]);
});