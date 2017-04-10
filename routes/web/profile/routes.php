<?php

Route::get('/emails/verify/{token}', [
    'as' => 'emails.verify',
    'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@verifyEmail'
]);

Route::get('/emails/verified', [
    'as' => 'emails.verified',
    'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@verifiedEmail'
]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/profile', [
        'as' => 'profile.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@index'
    ]);
    Route::post('/profile', [
        'as' => 'profile.index.update',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@postProfile'
    ]);
    Route::get('/profile/emails', [
        'as' => 'profile.emails.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@getEmails'
    ]);
    Route::get('/profile/notifications', [
        'as' => 'profile.notifications.edit',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@getNotifications'
    ]);
    Route::post('/profile/notifications', [
        'as' => 'profile.notifications.update',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@postNotifications'
    ]);
    Route::get('/profile/shippingprofile', [
        'as' => 'profile.shippingprofile.edit',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@getShippingProfile'
    ]);
    Route::get('/profile/socialprofiles', [
        'as' => 'profile.social.edit',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@getSocial'
    ]);
    Route::post('/profile/socialprofiles', [
        'as' => 'profile.social.update',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class.'@postSocial'
    ]);

    Route::get('/claims', [
        'as' => 'profile.claims.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileClaimsController::class.'@index'
    ]);

    Route::get('/purchases/{itemID}', [
        'as' => 'profile.claims.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileClaimsController::class.'@show'
    ]);

    Route::get('/profile/subscription', [
        'as' => 'profile.subscription.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionsController::class.'@index'
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
    Route::get('/profile/subscription/cancel', [
        'as' => 'profile.subscription.cancel',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionsController::class.'@edit'
    ]);
    Route::delete('/profile/subscription', [
        'as' => 'profile.subscription.destroy',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionsController::class.'@destroy'
    ]);
    Route::post('/profile/subscription/{subscription}', [
        'as' => 'profile.subscription.store',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionsController::class.'@store'
    ]);
    Route::get('/profile/subscription/{subscription}', [
        'as' => 'profile.subscription.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileSubscriptionsController::class.'@show'
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
        'middleware' => 'subscribed:merchant|merchant_plus',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileCreditsController::class.'@index'
    ]);
    Route::post('profile/credits', [
        'as' => 'profile.credits.store',
        'middleware' => 'subscribed:merchant|merchant_plus',
        'uses' => \Kabooodle\Http\Controllers\Web\Profile\ProfileCreditsController::class.'@store'
    ]);
});