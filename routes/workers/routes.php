<?php

Route::post('workers/schedule', [
    'as' => 'workers.schedule',
    'uses' => \Kabooodle\Http\Web\Workers\WorkersController::class.'@cron'
]);
