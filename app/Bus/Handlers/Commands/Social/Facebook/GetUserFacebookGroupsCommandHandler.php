<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Social\Facebook;

use DB;
use Kabooodle\Models\User;
use Kabooodle\Models\FacebookNodes;
use Kabooodle\Bus\Events\CacheMissEvent;
use Facebook\Exceptions\FacebookSDKException;
use Kabooodle\Bus\Commands\Social\Facebook\GetUserFacebookGroupsCommand;

/**
 * Class GetUserFacebookGroupsCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Social\Facebook
 */
class GetUserFacebookGroupsCommandHandler extends UserFacebookCache
{
    /**
     * @var
     */
    public $existingNodesFromDB;

    /**
     * @param GetUserFacebookGroupsCommand $command
     * @return bool|\Facebook\GraphNodes\GraphEdge|mixed
     * @throws FacebookSDKException
     * @throws \Exception
     */
    public function handle(GetUserFacebookGroupsCommand $command)
    {
        $tag = self::TAG;

        /** @var User $actor */
        $actor = $command->getActor();

        // Allows you override returning a cached response.
        $forceRefresh = $command->isForcedRefresh();

        // Does the user even have facebook configured?
        if (! $actor->getFacebookUserId() || ! $actor->getFacebookUserToken()) {
            return false;
        }

        if(! $forceRefresh) {

            // Do we have a cached response? Return it if we do.
            if($this->cache->tags($tag)->has($actor->getFacebookUserId())) {
                return $this->cache->tags($tag)->get($actor->getFacebookUserId());
            }

            event(new CacheMissEvent($tag, $actor->getFacebookUserId()));
        }

        try {
            // Fetch a fresh FB response.
            $groups = $this->facebook->getUsersGroupsWithAlbums($actor->getFacebookUserId());

            // Cache all the node ids for later use.
            $nodeIds = [];

            if ($groups) {
                $groups = $groups->asArray();
                usort($groups, function($a, $b){
                    return strcasecmp($a['name'], $b['name']);
                });

                foreach ($groups as $key => $group) {

                    // If the user cannot administrate, delete the group.
                    if ($group['administrator'] === false) {
                        unset($groups[$key]);
                        continue;
                    }

                    $nodeIds[] = $group['id'];

                    // If the group has albums, lets make sure the album can be uploaded to.
                    if (isset($group['albums'])) {
                        foreach ($group['albums'] as $albumKey => $album) {
                            if ($album['can_upload'] === false) {
                                unset($group['albums'][$albumKey]);
                            }
                            $nodeIds[] = $album['id'];
                        }

                        usort($group['albums'], function($a, $b){
                            return strcasecmp($a['name'], $b['name']);
                        });

                    } else {
                        // Create an empty albums key with an empty array.
                        $group['albums'] = [];
                    }
                }
            }

            // At this point, we have filtered through and now have the desired group and albums.
            // Lets store this in the DB and then cache the filtered data.
            $this->cache->tags($tag)->put($actor->getFacebookUserId(), $groups, config('session.lifetime'));

            // Build a collection of FacebookNodes whereIn (nodeids) that we can defer to later.
            // Upon storing to the DB, if the node_id already exists in the DB, then we will perform
            // an update, vs creating a new entry.
            $this->setAllExistingNodesFromDB($nodeIds);

            // Store all the group and albums into the db.
            $this->storeGroupsAndAlbums($groups, $actor->id);

            return $groups;
        } catch (FacebookSDKException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $fbGroups
     * @param $userId
     * @return array
     */
    public function storeGroupsAndAlbums($fbGroups, $userId)
    {
        $storedNodes = [];
        foreach($fbGroups as $group) {
            $storedGroup = $this->storeFbNode($group['id'], $group['name']);

            $this->checkAndAttachUserToNode($storedGroup, $userId, $storedGroup->facebook_node_type);

            $storedNodes[] = $storedGroup;
            if (isset($group['albums'])) {
                if (count($group['albums'])) {
                    foreach ($group['albums'] as $album) {
                        $storedAlbum = $this->storeFbNode($album['id'], $album['name'], FacebookNodes::NODE_ALBUM,
                            $storedGroup->id);

                        $this->checkAndAttachUserToNode($storedAlbum, $userId, FacebookNodes::NODE_ALBUM);

                        $storedNodes[] = $storedAlbum;
                    }
                }
            }
        }

        return $storedNodes;
    }

    /**
     * Check if the stored node ^ exists in the already existing nodes.
     * If it doesn't, then we know we need to attach the user to it.
     * It it does, see if its already attached to the user.
     *
     * @param $node
     * @param $userId
     * @param $nodeType
     * @return node
     */
    public function checkAndAttachUserToNode($node, $userId, $nodeType)
    {
        $exists = $this->existingNodesFromDB->find($node->id);
        if (! $exists || ! $exists->users->contains($userId)) {
            $node->users()->attach($userId, [
                'facebook_node_id' => $node->facebook_node_id,
                'node_type' => $nodeType
            ]);
        }

        return $node;
    }

    /**
     * @param $id
     * @param $name
     * @param string $nodeType
     * @param null $parentId
     * @return FacebookNodes
     */
    public function storeFbNode($id, $name, $nodeType = FacebookNodes::NODE_GROUP, $parentId = null)
    {
        if (! $entry = $this->doesFbNodeExistInDB($id)) {
            $entry = new FacebookNodes;
            $entry->facebook_node_id = $id;
            $entry->facebook_parent_node_id = $parentId;
            $entry->facebook_node_type = $nodeType;
        }

        $entry->facebook_node_name = $name;
        $entry->save();

        return $entry;
    }

    /**
     * @param array $nodeIds
     */
    public function setAllExistingNodesFromDB(array $nodeIds)
    {
        $nodes = FacebookNodes::whereIn('facebook_node_id', $nodeIds)->with('users')->get();

        $this->existingNodesFromDB = $nodes;
    }

    /**
     * @param $nodeId
     * @return mixed
     */
    public function doesFbNodeExistInDB($nodeId)
    {
        return $this->existingNodesFromDB->where('facebook_node_id', (int) $nodeId)->first();
    }
}
