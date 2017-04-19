<?php

Route::get('/auth/register', [
    'as' => 'auth.register',
    'uses' => \Kabooodle\Http\Controllers\Web\Auth\AuthController::class.'@getRegister'
]);

Route::post('/auth/register', [
    'as' => 'auth.register.store',
    'uses' => \Kabooodle\Http\Controllers\Web\Auth\AuthController::class.'@postRegister'
]);

Route::post('/auth/guest-convert', [
    'as' => 'auth.guest-convert',
    'uses' => \Kabooodle\Http\Controllers\Web\Auth\AuthController::class.'@guestConvert'
]);

Route::get('/auth/login', [
    'as' => 'auth.login',
    'uses' => \Kabooodle\Http\Controllers\Web\Auth\AuthController::class.'@getLogin'
]);

Route::post('/auth/login', [
    'as' => 'auth.login.store',
    'uses' => \Kabooodle\Http\Controllers\Web\Auth\AuthController::class.'@postLogin'
]);

Route::post('/auth/logout', [
    'as' => 'auth.logout',
    'uses' => \Kabooodle\Http\Controllers\Web\Auth\AuthController::class.'@logout'
]);

Route::get('/auth/password/reset/{token?}', [
    'as' => 'auth.password.reset.index',
    'uses' => \Kabooodle\Http\Controllers\Web\Auth\PasswordController::class.'@showResetForm'
]);

Route::post('/auth/password/email', [
    'as' =>'auth.password.reset.send',
    'uses' => \Kabooodle\Http\Controllers\Web\Auth\PasswordController::class.'@sendResetLinkEmail'
]);

Route::post('/auth/password/reset', [
    'as' =>'auth.password.reset.reset',
    'uses' => \Kabooodle\Http\Controllers\Web\Auth\PasswordController::class.'@reset'
]);
