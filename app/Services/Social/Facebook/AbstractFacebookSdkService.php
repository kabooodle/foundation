<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Social\Facebook;

use Kabooodle\Models\User;
use Facebook\Exceptions\FacebookSDKException;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

/**
 * Class AbstractFacebookSdkService
 */
class AbstractFacebookSdkService extends LaravelFacebookSdk
{
    /**
     * @param AccessToken|string|null $accessToken
     *
     * @return \Facebook\GraphNodes\GraphNode|bool
     */
    public function testAccessToken($accessToken = null)
    {
        try {
            $request = $this->get('/me', $accessToken ?: user()->getFacebookUserToken());

            return $request->getGraphNode();
        } catch (FacebookSDKException $e) {
            return false;
        }
    }

    /**
     * @param null $userId
     * @param User|null $user
     * @return \Facebook\GraphNodes\GraphEdge
     */
    public function getUsersGroups($userId = null, User $user = null )
    {
        $userId = $userId ?: user()->getFacebookUserId();
        $user = $user ?: user();
        $request = $this->get("/{$userId}/groups?fields=administrator,id,name", $user->getFacebookUserToken());

        return $request->getGraphEdge();
    }

    /**
     * @param null $userId
     * @param User|null $user
     * @return \Facebook\GraphNodes\GraphEdge
     */
    public function getUsersGroupsWithAlbums($userId = null, User $user = null )
    {
        $userId = $userId ?: user()->getFacebookUserId();
        $user = $user ?: user();
        $request = $this->get("/{$userId}/groups?fields=administrator,albums.limit(100000){id,can_upload,name},id,name,limit=100000", $user->getFacebookUserToken());

        return $request->getGraphEdge();
    }

    /**
     * @param $groupId
     * @param User|null $user
     * @return \Facebook\GraphNodes\GraphEdge
     */
    public function getGroupAlbums($groupId, User $user = null)
    {
        $user =  $user ?: user();
        $request = $this->get("/{$groupId}/albums?fields=id,name,updated_time,type,photo_count,limit=100000", $user->getFacebookUserToken());

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
     * @param string $accessToken
     *
     * @return \Facebook\Authentication\AccessToken
     */
    public function getLongLivedAccessToken(string $accessToken)
    {
        $client = $this->getOAuth2Client();

        return $client->getLongLivedAccessToken($accessToken);
    }
}
