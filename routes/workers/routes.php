<?php

Route::get('workers/schedule/{key}', [
    'as' => 'workers.schedule',
    'uses' => \Kabooodle\Http\Controllers\Web\Workers\WorkersController::class.'@cron'
]);
