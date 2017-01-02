<?php
Route::group(['middleware' => 'auth'], function () {
    Route::get('messenger', [
        'as' => 'messenger.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Messenger\MessengerController::class.'@index'
    ]);
    Route::get('messenger/create', [
        'as' => 'messenger.create',
        'uses' => \Kabooodle\Http\Controllers\Web\Messenger\MessengerController::class.'@create'
    ]);
    Route::post('messenger/create', [
        'as' => 'messenger.store',
        'uses' => \Kabooodle\Http\Controllers\Web\Messenger\MessengerController::class.'@store'
    ]);
    Route::get('messenger/{thread}', [
        'as' => 'messenger.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Messenger\MessengerController::class.'@show'
    ]);
});