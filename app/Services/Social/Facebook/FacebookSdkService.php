<?php

namespace Kabooodle\Services\Social\Facebook;

use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

/**
 * Class FacebookSdkService
 * @package Kabooodle\Services\Social\Facebook
 */
class FacebookSdkService extends LaravelFacebookSdk
{
    /**
     * @param AccessToken|string|null $accessToken
     *
     * @return \Facebook\FacebookResponse|bool
     */
    public function testToken($accessToken = null)
    {
        try {
            return $this->get('/me', $accessToken ? : user()->facebook_access_token);
        } catch (FacebookSDKException $e) {
            return false;
        }
    }
}
