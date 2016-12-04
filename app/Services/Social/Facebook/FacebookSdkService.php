<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Services\Social\Facebook;

use Facebook\FacebookResponse;
use Kabooodle\Models\FacebookNodes;
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
     * @return \Facebook\GraphNodes\GraphNode|bool
     */
    public function testAccessToken($accessToken = null)
    {
        try {
            $request = $this->get('/me', $accessToken ?: user()->getFacebookUserId());

            return $request->getGraphNode();
        } catch (FacebookSDKException $e) {
            return false;
        }
    }

    /**
     * @param null|int $userId
     *
     * @return \Facebook\GraphNodes\GraphEdge
     */
    public function getUsersGroups($userId = null)
    {
        $userId = $userId ?: user()->getFacebookUserId();
        $request = $this->get("/{$userId}/groups?fields=administrator,albums{id,can_upload,name},id,name", user()->getFacebookUserToken());

        return $request->getGraphEdge();
    }

    /**
     * @param null|int $groupId
     *
     * @return \Facebook\GraphNodes\GraphEdge
     */
    public function getGroupAlbums($groupId)
    {
        $request = $this->get("/{$groupId}/albums?fields=id,name,updated_time,type,photo_count",
            user()->getFacebookUserToken());

        return $request->getGraphEdge();
    }

    /**
     * @param       $albumId
     * @param array $params
     * @param null  $userToken
     *
     * @return \Facebook\GraphNodes\GraphNode
     */
    public function postPhotoToGroupAlbum($albumId, $params = [], $userToken = null)
    {
        $request = $this->post("/{$albumId}/photos", $params, ($userToken ?: user()->getFacebookUserToken()));

        return $request->getGraphNode();
    }

    /**
     * @param       $photoId
     * @param array $params
     * @param null  $userToken
     *
     * @return \Facebook\GraphNodes\GraphNode
     */
    public function deletePhoto($photoId, array $params = [], $userToken = null)
    {
        $request = $this->delete("/{$photoId}", $params, ($userToken ?: user()->getFacebookUserToken()));

        return $request->getGraphNode();
    }

    /**
     * @param       $photoId
     * @param array $params
     * @param null  $userToken
     *
     * @return \Facebook\GraphNodes\GraphNode
     */
    public function postCommentToPhoto($photoId, array $params = [], $userToken = null)
    {
        $request = $this->post("/{$photoId}/comments", $params, ($userToken ?: user()->getFacebookUserToken()));

        return $request->getGraphNode();
    }

    /**
     * Sends a request to Graph and returns the result.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @param AccessToken|string|null $accessToken
     * @param string|null $eTag
     * @param string|null $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function sendRequest(
        $method,
        $endpoint,
        array $params = [],
        $accessToken = null,
        $eTag = null,
        $graphVersion = null
    ) {
        return parent::sendRequest($method, $endpoint, $params, $accessToken, $eTag, $graphVersion);
    }
}
