<?php

Route::group(['middleware' => 'auth'], function () {
    Route::resource('groups', \Kabooodle\Http\Controllers\Web\Groups\GroupsController::class);
    Route::resource('groups.followers', \Kabooodle\Http\Controllers\Web\Groups\GroupsFollowersController::class);
    Route::resource('groups.members', \Kabooodle\Http\Controllers\Web\Groups\GroupsMembersController::class);
});