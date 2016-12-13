<?php

Route::group(['middleware' => 'auth'], function () {
    Route::resource('watching.items', \Kabooodle\Http\Controllers\Web\Purchases\WatchingController::class, [
        'only' => ['index', 'destroy'],
        'parameters' => ['watching' => 'username']
    ]);
});