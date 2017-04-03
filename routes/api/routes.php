<?php

$route->get('/', [
    'as' => 'api.index',
    'uses' => \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping'
]);


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->group(['middleware' => 'api_allow_auth_and_non'], function($api){
        $api->get('files', ['as' =>'api.files.sign', 'uses' => \Kabooodle\Http\Controllers\Api\Files\FilesApiController::class.'@createPresignedData']);

        $api->get('/ping', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping');
        $api->get('/version', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@version');
        $api->post('/auth/login', [
            'as' => 'auth.login.store',
            'uses' => \Kabooodle\Http\Controllers\Api\Auth\AuthApiController::class.'@login'
        ]);
        $api->post('auth/register', [
            'as' => 'auth.register',
            'uses' => \Kabooodle\Http\Controllers\Api\Auth\AuthApiController::class . '@register'
        ]);
        $api->post('auth/guest-convert', [
            'as' => 'auth.guest-convert',
            'uses' => \Kabooodle\Http\Controllers\Api\Auth\AuthApiController::class . '@guestConvert'
        ]);
        $api->post('views', [
            'as' => 'views.store',
            'uses' => \Kabooodle\Http\Controllers\Api\Views\ViewsController::class.'@store'
        ]);
        $api->post('closed-beta', [
            'as' => 'closed-beta.store',
            'middleware' => 'api.throttle',
            'limit' => 100,
            'expires' => 5,
            'uses' => \Kabooodle\Http\Controllers\Api\GeneralController::class.'@joinClosedBeta'
        ]);
        $api->post('contact', [
            'as' => 'contact.store',
            'middleware' => 'api.throttle',
            'limit' => 100,
            'expires' => 5,
            'uses' => \Kabooodle\Http\Controllers\Api\Contact\ContactController::class.'@store'
        ]);

        // TODO: Replace require_once to require when caching routes.
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'queues' . DIRECTORY_SEPARATOR . 'routes.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'inventory' . DIRECTORY_SEPARATOR . 'routes.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'inventory-groupings' . DIRECTORY_SEPARATOR . 'routes.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'routes.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'listables' . DIRECTORY_SEPARATOR . 'routes.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'listings' . DIRECTORY_SEPARATOR . 'routes.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'flashsales'.DIRECTORY_SEPARATOR . 'routes.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'follows'.DIRECTORY_SEPARATOR . 'routes.php';

        $api->group(['middleware' => 'jwt.auth'], function ($api) {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'claims' . DIRECTORY_SEPARATOR . 'routes.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'addresses' . DIRECTORY_SEPARATOR . 'routes.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'emails' . DIRECTORY_SEPARATOR . 'routes.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR . 'routes.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'social' . DIRECTORY_SEPARATOR . 'routes.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'notices' . DIRECTORY_SEPARATOR . 'routes.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'messenger' . DIRECTORY_SEPARATOR . 'routes.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'phonenumbers' . DIRECTORY_SEPARATOR . 'routes.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'shipping' . DIRECTORY_SEPARATOR . 'routes.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'referrals' . DIRECTORY_SEPARATOR . 'routes.php';
        });

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'routes.php';
    });
});
