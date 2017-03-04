<?php

Route::post('/workers/schedule/{key}', [
    'as' => 'workers.schedule',
    'uses' => \Kabooodle\Http\Controllers\Web\Workers\WorkersController::class.'@fb'
]);
Route::post('/workers/schedule_deletion/{key}', [
    'as' => 'workers.schedule.deletion',
    'uses' => \Kabooodle\Http\Controllers\Web\Workers\WorkersController::class.'@fbDeletion'
]);
Route::post('/workers/checktrials/{key}', [
    'as' => 'workers.checktrials',
    'uses' => \Kabooodle\Http\Controllers\Web\Workers\WorkersController::class.'@checktrials'
]);

