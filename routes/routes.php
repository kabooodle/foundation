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

//// Generate a login URL
//        Route::get('/facebook/login', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
//        {
//            // Send an array of permissions to request
//            $login_url = $fb->getLoginUrl();
//
//            // Obviously you'd do this in blade :)
//            echo '<a href="' . $login_url . '">Login with Facebook</a>';
//        });
//
//// Endpoint that is redirected to after an authentication attempt
//        Route::get('/facebook/callback', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
//        {
//            // Obtain an access token.
//            try {
//                $token = $fb->getAccessTokenFromRedirect();
//            } catch (Facebook\Exceptions\FacebookSDKException $e) {
//                dd($e->getMessage());
//            }
//
//            // Access token will be null if the user denied the request
//            // or if someone just hit this URL outside of the OAuth flow.
//            if (! $token) {
//                // Get the redirect helper
//                $helper = $fb->getRedirectLoginHelper();
//
//                if (! $helper->getError()) {
//                    abort(403, 'Unauthorized action.');
//                }
//
//                // User denied the request
//                dd(
//                    $helper->getError(),
//                    $helper->getErrorCode(),
//                    $helper->getErrorReason(),
//                    $helper->getErrorDescription()
//                );
//            }
//
//            if (! $token->isLongLived()) {
//                // OAuth 2.0 client handler
//                $oauth_client = $fb->getOAuth2Client();
//
//                // Extend the access token.
//                try {
//                    $token = $oauth_client->getLongLivedAccessToken($token);
//                } catch (Facebook\Exceptions\FacebookSDKException $e) {
//                    dd($e->getMessage());
//                }
//            }
//
//            $fb->setDefaultAccessToken($token);
//
//            // Save for later
//            Session::put('fb_user_access_token', (string) $token);
//
//            // Get basic info on the user from Facebook.
//            try {
//                $response = $fb->get('/me?fields=id,name,email');
//            } catch (Facebook\Exceptions\FacebookSDKException $e) {
//                dd($e->getMessage());
//            }
//
//            // Convert the response to a `Facebook/GraphNodes/GraphUser` collection
//            $facebook_user = $response->getGraphUser();
//
//            // Create the user if it does not exist or update the existing entry.
//            // This will only work if you've added the SyncableGraphNodeTrait to your User model.
//            $user = \Kabooodle\Models\User::createOrUpdateGraphNode($facebook_user);
//
//            // Log the user into Laravel
//            Auth::login($user);
//
//            return redirect('/')->with('message', 'Successfully logged in with Facebook');
//        });
//    });
//
//    Route::get('facebook/test', function(){
//        $fb = app(SammyK\LaravelFacebookSdk\LaravelFacebookSdk::class);
//
//        try {
//            $response = $fb->get('/me?fields=id,name,email', Session::get('fb_user_access_token'));
//        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
//            dd($e->getMessage());
//        }
//
//        $userNode = $response->getGraphUser();
//        printf('Hello, %s!', $userNode->getName());
//    });
    });
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'inventory' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'flashsales' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR . 'routes.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'social' . DIRECTORY_SEPARATOR . 'facebook-routes.php';
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

        $api->group(['middleware' => 'jwt.auth'], function($api){
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'api-routes.php';
        });
    });
});

