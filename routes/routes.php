<?php
//Route::get('/', function () {
//
//    $x = new \Kabooodle\Libraries\LLRClient\LLRClient;
//    $r = $x->login(new \Kabooodle\Libraries\LLRClient\LLRCredentials(\Kabooodle\Models\LLRUser::find(1)));
//
//    dd($r, $x->getConnectionError());
//});

Route::get('/', function(){
    if (! user()) {
        return redirect()->route('auth.login');
    } else {
        return 'hi';
    }
});

Route::get('privacy', function(){
   return view('content.privacy');
});

Route::group(['middleware' => ['web']], function() {
    Route::post(
        '__captainHook/shtriwpe',
        \Kabooodle\Http\Controllers\Web\Webhooks\StripeWebhooksController::class . '@handleWebhook'
    );
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


    Route::get('/referrals', [
        'as' => 'referrals.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Referrals\ReferralsController::class.'@index'
    ]);

    Route::get('c/{facebookItemString}', [
        'as' => 'externalclaim.show',
        'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryClaimsFacebookController::class.'@show'
    ]);

    Route::post('c/{facebookItemString}', [
        'as' => 'externalclaim.claim',
        'uses' => \Kabooodle\Http\Controllers\Web\Shop\Inventory\InventoryClaimsFacebookController::class.'@claim'
    ]);
});


Route::group(['domain' => 'api.'.getEnvDomain(true)], function($route){
    $route->get('/', [
        'as' => 'api.index',
        'uses' => \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping'
    ]);

    $route->get('files', ['as' =>'api.files.sign', 'uses' => \Kabooodle\Http\Controllers\Api\Files\FilesApiController::class.'@createPresignedData']);

    $api = app('Dingo\Api\Routing\Router');

    $api->version('v1', function ($api) {
        $api->get('/ping', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@ping');
        $api->get('/version', \Kabooodle\Http\Controllers\Api\GeneralController::class.'@version');
        $api->post('/auth/login', \Kabooodle\Http\Controllers\Api\Auth\AuthApiController::class.'@login');

        $api->group(['middleware' => 'jwt.auth'], function($api){
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'api-routes.php';
        });
    });
});

