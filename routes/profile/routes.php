<?php

Route::group(['middleware' => 'auth'], function () {
    Route::resource('profile', \Kabooodle\Http\Controllers\Web\Profile\ProfileSettingsController::class);
});