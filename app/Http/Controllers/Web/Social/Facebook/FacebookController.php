<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Social\Facebook;

use Messages;
use Illuminate\Http\Request;
use Facebook\Exceptions\FacebookSDKException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;

/**
 * Class FacebookController
 * @package Kabooodle\Http\Controllers\Web\Social\Facebook
 */
class FacebookController extends Controller
{
    /**
     * FacebookController constructor.
     *
     * @param FacebookSdkService $facebookSdk
     */
    public function __construct(FacebookSdkService $facebookSdk)
    {
        $this->fb = $facebookSdk;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        $fb = $this->fb;

        try {
            $token = $fb->getAccessTokenFromRedirect();
        } catch (FacebookSDKException $e) {
            dd($e->getMessage());
        }

        // Access token will be null if the user denied the request
        // or if someone just hit this URL outside of the OAuth flow.
        if (! $token) {
            // Get the redirect helper
            $helper = $fb->getRedirectLoginHelper();

            if (! $helper->getError()) {
                abort(403, 'Unauthorized action.');
            }

            return redirect()->route('profile.index');
            // User denied the request
//            dd(
//                $helper->getError(),
//                $helper->getErrorCode(),
//                $helper->getErrorReason(),
//                $helper->getErrorDescription()
//            );
        }

        if (! $token->isLongLived()) {
            // OAuth 2.0 client handler
            $oauth_client = $fb->getOAuth2Client();

            // Extend the access token.
            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (FacebookSDKException $e) {
                dd($e->getMessage());
            }
        }

        $fb->setDefaultAccessToken($token);

        try {
            $response = $fb->get('/me?fields=id,name,email');
        } catch (FacebookSDKException $e) {
            dd($e->getMessage());
        }

        /** @var \Facebook\GraphNodes\GraphUser $facebook_user */
        $facebook_user = $response->getGraphUser();

        $user = user();
        $user->facebook_user_id = $facebook_user->getId();
        $user->facebook_access_token = (string) $token;
        $user->facebook_access_token_expires = $token->getExpiresAt();
        $user->save();

        Messages::success('Connection to Facebook successful!');

        return redirect()->route('profile.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke()
    {
        $revoked = $this->fb->delete('/me/permissions', [],  user()->facebook_access_token);

        $user = user();
        $user->facebook_user_id = null;
        $user->facebook_access_token = null;
        $user->facebook_access_token_expires = null;
        $user->save();

        Messages::success('Connection to Facebook removed.');

        return redirect()->route('profile.index');
    }
}
