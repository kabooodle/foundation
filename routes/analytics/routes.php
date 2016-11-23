<?php

Route::group(['middleware' => ['auth']], function () {

    Route::get('analytics', [
        'as' => 'analytics.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Analytics\AnalyticsController::class.'@index'
    ]);

    Route::match(['get', 'post'], '/analytics/sales', [
        'as' => 'analytics.sales.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Analytics\AnalyticsController::class.'@sales'
    ]);

    Route::match(['get', 'post'], '/analytics/postings', [
       'as' => 'analytics.postings.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Analytics\AnalyticsController::class.'@postings'
    ]);
});