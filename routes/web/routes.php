<?php
//Route::get('/', function () {
//
//    $x = new \Kabooodle\Libraries\LLRClient\LLRClient;
//    $r = $x->login(new \Kabooodle\Libraries\LLRClient\LLRCredentials(\Kabooodle\Models\LLRUser::find(1)));
//
//    dd($r, $x->getConnectionError());
//});

//Route::get('/', function(){
//    return redirect('http://www.kabooodle.com');
//});

Route::get('privacy', function(){
   return view('content.privacy');
});

Route::post(
    '__captainHook/shtriwpe',
    \Kabooodle\Http\Controllers\Web\Webhooks\StripeWebhooksController::class . '@handleWebhook'
);
Route::post(
    '__captainHook/sheepoo',
    \Kabooodle\Http\Controllers\Web\Webhooks\ShippoWebhooksController::class . '@handleWebhook'
);
Route::get('c/{hash}', [
    'as' => 'externalclaim.show',
    'uses' => \Kabooodle\Http\Controllers\Web\Claims\ClaimingController::class.'@show'
]);

//Route::group(['domain' => getEnvDomain(true)], function(){

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', function () {
            return View::make('welcome');
        });
    });
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'claims' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'inventory' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'flashsales' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'social' . DIRECTORY_SEPARATOR . 'facebook-routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'shipping' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'analytics' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'listings' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'watching' . DIRECTORY_SEPARATOR . 'routes.php';

    Route::get('/referrals', [
        'as' => 'referrals.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Referrals\ReferralsController::class.'@index'
    ]);

    Route::get('/invite/{userName}', [
        'as' => 'invite.index',
        'uses' => \Kabooodle\Http\Controllers\Web\Referrals\ReferralsController::class.'@invite'
    ]);
//});
