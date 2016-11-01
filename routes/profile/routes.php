<?php

Route::group(['middleware' => 'auth'], function () {
    Route::get('/profile', [
        'as' => 'profile.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@index'
    ]);
    Route::post('/profile', [
        'as' => 'profile.index.update',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@postProfile'
    ]);
    Route::get('/profile/notifications', [
        'as' => 'profile.notifications.edit',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@getNotifications'
    ]);
    Route::post('/profile/notifications', [
        'as' => 'profile.notifications.update',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@postNotifications'
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
    Route::delete('/profile/subscription', [
        'as' => 'profile.subscription.destroy',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionsController::class.'@destroy'
    ]);
    Route::get('/profile/subscription/invoices', [
        'as' => 'profile.subscription.invoices.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionInvoicesController::class.'@index'
    ]);
    Route::get('/profile/subscription/invoices/{invoice}', [
        'as' => 'profile.subscription.invoices.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionInvoicesController::class.'@show'
    ]);
    Route::get('/profile/subscription/invoices/{invoice}/download', [
        'as' => 'profile.subscription.invoices.download',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionInvoicesController::class.'@download'
    ]);
    Route::get('/profile/creditcard', [
        'as' => 'profile.creditcard.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileCreditCardController::class.'@index'
    ]);
    Route::post('/profile/creditcard', [
        'as' => 'profile.creditcard.store',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileCreditCardController::class.'@store'
    ]);
    Route::get('profile/credits', [
        'as' => 'profile.credits.index',
        'middleware' => 'subscribed:main',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileCreditsController::class.'@index'
    ]);
    Route::post('profile/credits', [
        'as' => 'profile.credits.store',
        'middleware' => 'subscribed:main',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileCreditsController::class.'@store'
    ]);
});