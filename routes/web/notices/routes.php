<?php
Route::group(['middleware' => 'auth'], function () {
    Route::get('notices', [
        'as' => 'notices.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Notices\NoticesController::class.'@index'
    ]);
});