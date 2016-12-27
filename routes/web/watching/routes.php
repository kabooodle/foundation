<?php

Route::resource('watching.items', \Kabooodle\Http\Controllers\Web\Purchases\WatchingController::class, [
    'only' => ['index'],
    'parameters' => ['watching' => 'username']
]);