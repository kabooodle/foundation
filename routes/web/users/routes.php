<?php

Route::get('/users/{username}/listings',[
    'as' => 'users.listings.index',
    'uses' => \Kabooodle\Http\Controllers\Web\Users\UserListingsController::class.'@index'
]);
Route::get('/users/{username}/listings/{listing}', [
    'as' => 'users.listings.show',
    'uses' => \Kabooodle\Http\Controllers\Web\Users\UserListingsController::class.'@show'
]);

