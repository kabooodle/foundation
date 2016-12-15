<?php

Route::post('/workers/schedule/{key}', [
    'as' => 'workers.schedule',
    'uses' => \Kabooodle\Http\Controllers\Web\Workers\WorkersController::class.'@fb'
]);
Route::post('/workers/checktrials/{key}', [
    'as' => 'workers.checktrials',
    'uses' => \Kabooodle\Http\Controllers\Web\Workers\WorkersController::class.'@checktrials'
]);

