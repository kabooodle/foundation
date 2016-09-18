<?php

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
            $request = $this->get('/me', $accessToken ? : user()->getFacebookUserId());

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
        $userId = $userId ? : user()->getFacebookUserId();
        $request = $this->get("/{$userId}/groups", user()->getFacebookUserToken());

        return $request->getGraphEdge();
    }

    /**
     * @param null|int $groupId
     *
     * @return \Facebook\GraphNodes\GraphEdge
     */
    public function getGroupAlbums($groupId)
    {
        $request = $this->get("/{$groupId}/albums?fields=id,name,updated_time,type,photo_count", user()->getFacebookUserToken());

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
        $request = $this->post("/{$albumId}/photos", $params, ($userToken ? : user()->getFacebookUserToken()));

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
        $request = $this->delete("/{$photoId}", $params, ($userToken ? : user()->getFacebookUserToken()));

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
        $request = $this->post("/{$photoId}/comments", $params, ($userToken ? : user()->getFacebookUserToken()));

        return $request->getGraphNode();
    }

    /**
     * Sends a request to Graph and returns the result.
     *
     * @param string                  $method
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     * @param string|null             $eTag
     * @param string|null             $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function sendRequest($method, $endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null)
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;

        // Prepare a Facebook Request
        $request = $this->request($method, $endpoint, $params, $accessToken, $eTag, $graphVersion);

        // Send the Prepared Request to Facebook Graph
        $response = $this->client->sendRequest($request);

        // THIS IS BULLSHIT THROW AWAY CODE, JUST TESTING LOGIC HANDLING
        // AND FACEBOOKS BULLSHIT SDK
        if ($method  <> 'delete' && $endpoint <> '/me/permissions') {

            $x = new FacebookNodes;
//        $x->facebook_node = '';
//        $x->facebook_node_id = ''; // use regex to grab intvalue of endpoint

            $responseGraphEdge = $response->getGraphNode();
            $responseArray = $responseGraphEdge->asArray();
            $responseJson = $responseGraphEdge->asJson();

            $x->facebook_post_id = $responseArray['id'];
            $x->facebook_data = $responseJson;
            $x->save();
        }


        return $this->lastResponse = $response;
    }
}
