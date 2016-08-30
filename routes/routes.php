<?php

//Route::get('/', function () {
//
//    $x = new \Kabooodle\Libraries\LLRClient\LLRClient;
//    $r = $x->login(new \Kabooodle\Libraries\LLRClient\LLRCredentials(\Kabooodle\Models\LLRUser::find(1)));
//
//    dd($r, $x->getConnectionError());
//});

Route::group(['middleware' => ['web'], 'domain' => getEnvDomain(true)], function(){
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', function () {
            return View::make('welcome');
        });

        Route::get('xx', function(){
            event(new \Kabooodle\Bus\Events\TestEventBroadcaster('hello'));
        });

    });

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'inventory' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'flashsales' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'routes.php';
});


Route::group(['domain' => 'api.'.getEnvDomain(true)], function($route){
    $route->get('/', [
        'as' => 'api.index',
        'uses' => \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping'
    ]);

    $api = app('Dingo\Api\Routing\Router');

    $api->version('v1', function ($api) {
        $api->get('/ping', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping');
        $api->get('/version', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@version');
        $api->post('/auth/login', \Kabooodle\Http\Controllers\Api\Auth\AuthApiController::class.'@login');

        $api->group(['middleware' => 'jwt.auth|cors'], function($api){
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'api-routes.php';
        });
    });
});

