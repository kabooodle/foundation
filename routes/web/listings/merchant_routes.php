<?php


Route::group(['middleware' => ['auth', 'subscribed:merchant|merchant_plus'], 'prefix' => 'merchant'], function () {
    Route::get('listings', [
        'as' => 'merchant.listings.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\MerchantListingsController::class.'@index'
    ]);
    Route::get('listings/{listing}/edit', [
        'as' => 'merchant.listings.edit',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\MerchantListingsController::class.'@edit'
    ]);
    Route::patch('listings/{listing}/edit', [
        'as' => 'merchant.listings.update',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\MerchantListingsController::class.'@update'
    ]);
    Route::get('listings/create', [
        'as' => 'merchant.listings.create',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\MerchantListingsController::class.'@create'
    ]);
    Route::post('listings', [
       'as' => 'merchant.listings.store',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\MerchantListingsController::class.'@store'
    ]);
    Route::get('listings/{listing}', [
        'as' => 'merchant.listings.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\MerchantListingsController::class.'@show'
    ]);
    Route::get('listings/{listing}/{group}/{groupid?}', [
        'as' => 'merchant.listings.group.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Listings\MerchantListingsController::class.'@detailed'
    ]);
});