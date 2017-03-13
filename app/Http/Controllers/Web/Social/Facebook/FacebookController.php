<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Social\Facebook;

use Messages;
use Exception;
use Illuminate\Http\Request;
use Facebook\FacebookResponse;
use Facebook\Exceptions\FacebookSDKException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Bus\Events\Social\UserFacebookCredentialsRevokedEvent;
use Kabooodle\Bus\Events\Social\UserFacebookCredentialsConnectedEvent;

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
            Messages::error('An error occurred getting token, please try again.');
            return redirect()->route('profile.social.edit');
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
        }

        if (! $token->isLongLived()) {
            // OAuth 2.0 client handler
            $oauth_client = $fb->getOAuth2Client();

            // Extend the access token.
            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (FacebookSDKException $e) {
                Messages::error('An error occurred, please try again.');
                return redirect()->route('profile.social.edit');
            }
        }

        try {
            $fb->setDefaultAccessToken($token);

            $response = $fb->get('/me?fields=id,name,email');

            /** @var \Facebook\GraphNodes\GraphUser $facebook_user */
            $facebookUser = $response->getGraphUser();

            $user = webUser();
            $user->facebook_user_id = $facebookUser->getId();
            $user->facebook_access_token = (string) $token;
            $user->facebook_access_token_expires = $token->getExpiresAt();
            $user->save();

            event(new UserFacebookCredentialsConnectedEvent($user));

            Messages::success('Connection to Facebook successful!');

            return redirect()->route('profile.social.edit');
        } catch (FacebookSDKException $e) {
            Messages::error('An error occurred getting your data, please try again.');
            return redirect()->route('profile.social.edit');
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke()
    {
        try {
            /** @var FacebookResponse $revoked */
            $this->fb->delete('/me/permissions', [],  webUser()->facebook_access_token);
            event(new UserFacebookCredentialsRevokedEvent(webUser()));

        } catch (Exception $e) { }
        // The reason our default is to indicate "success on revoke" is because, we really dont care.
        // That is to say, if revocation fails, we will still delete the local facebook token data anyway,
        // so that we dont use it again and the user is therefore forced to login again to FB.

        Messages::success('Connection to Facebook revoked.');

        $user = webUser();
        $user->facebook_access_token = null;
        $user->facebook_access_token_expires = null;
        $user->save();
        return redirect()->route('profile.social.edit');
    }
}
