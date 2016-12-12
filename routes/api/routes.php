<?php

$route->get('/', [
    'as' => 'api.index',
    'uses' => \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping'
]);

$route->get('files', ['as' =>'api.files.sign', 'uses' => \Kabooodle\Http\Controllers\Api\Files\FilesApiController::class.'@createPresignedData']);

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->get('/ping', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping');
    $api->get('/version', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@version');
    $api->post('/auth/login', [
        'as' => 'auth.login.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Auth\AuthApiController::class.'@login'
    ]);
    $api->post('pageviews', [
        'as' => 'inventory.pageviews.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Inventory\InventoryViewsController::class.'@store'
    ]);

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'inventory' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'routes.php';

    $api->group(['middleware' => 'jwt.auth'], function ($api) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR . 'routes.php';
    });

});
