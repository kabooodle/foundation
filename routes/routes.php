<?php
//Route::get('/', function () {
//
//    $x = new \Kabooodle\Libraries\LLRClient\LLRClient;
//    $r = $x->login(new \Kabooodle\Libraries\LLRClient\LLRCredentials(\Kabooodle\Models\LLRUser::find(1)));
//
//    dd($r, $x->getConnectionError());
//});

Route::group(['middleware' => ['web'], 'domain' => getEnvDomain(true)], function(){
    Route::get('/', function(){
        return redirect('http://www.kabooodle.com');
    });
});

Route::get('privacy', function(){
   return view('content.privacy');
});

Route::group(['middleware' => ['web']], function() {
    Route::post(
        '__captainHook/shtriwpe',
        \Kabooodle\Http\Controllers\Web\Webhooks\StripeWebhooksController::class . '@handleWebhook'
    );
    Route::post(
        '__captainHook/sheepoo',
        \Kabooodle\Http\Controllers\Web\Webhooks\ShippoWebhooksController::class . '@handleWebhook'
    );
    Route::get('c/{hash}', \Kabooodle\Http\Controllers\Web\Claims\ClaimingController::class.'@show');
});

Route::group(['middleware' => ['web'], 'domain' => 'app.'.getEnvDomain(true)], function(){

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', function () {
            return View::make('welcome');
        });
    });
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'inventory' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'flashsales' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'social' . DIRECTORY_SEPARATOR . 'facebook-routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'shipping' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'analytics' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'listings' . DIRECTORY_SEPARATOR . 'routes.php';

    Route::get('/referrals', [
        'as' => 'referrals.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Referrals\ReferralsController::class.'@index'
    ]);

    Route::get('/invite/{userName}', [
        'as' => 'invite.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Referrals\ReferralsController::class.'@invite'
    ]);
});

Route::group(['domain' => 'api.'.getEnvDomain(true)], function($route){
    $route->get('/', [
        'as' => 'api.index',
        'uses' => \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping'
    ]);

    $route->get('files', ['as' =>'api.files.sign', 'uses' => \Kabooodle\Http\Controllers\Api\Files\FilesApiController::class.'@createPresignedData']);

    $api = app(Dingo\Api\Routing\Router::class);

    $api->version('v1', function ($api) {
        $api->get('/ping', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping');
        $api->get('/version', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@version');
        $api->post('/auth/login', \Kabooodle\Http\Controllers\Api\Auth\AuthApiController::class.'@login');

        $api->group(['middleware' => 'jwt.auth'], function($api){
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'api-routes.php';
        });
    });
});

