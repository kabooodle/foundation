<?php

Route::group(['middleware' => 'auth'], function () {
    Route::get('/social/facebook/callback', \Kabooodle\Http\Controllers\Web\Social\Facebook\FacebookController::class.'@callback');
    Route::get('/social/facebook/revoke', \Kabooodle\Http\Controllers\Web\Social\Facebook\FacebookController::class.'@revoke');
});