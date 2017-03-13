<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Social\Facebook;

use Exception;
use Carbon\Carbon;
use Kabooodle\Models\FacebookItems;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Services\Social\Facebook\Entities\PhotoDescription;
use Kabooodle\Bus\Commands\Social\Facebook\PostPhotoToGroupAlbumCommand;

/**
 * Class PostPhotoToGroupAlbumCommand
 * @package Kabooodle\Bus\Social\Facebook
 */
class PostPhotoToGroupAlbumCommandHandler
{
    /**
     * PostPhotoToGroupAlbumCommandHandler constructor.
     *
     * @param FacebookSdkService $facebook
     */
    public function __construct(FacebookSdkService $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @param PostPhotoToGroupAlbumCommand $command
     *
     * @return void
     *
     * @throws Exception
     */
    public function handle(PostPhotoToGroupAlbumCommand $command)
    {
        try {
            $facebookItem = FacebookItems::where('id', $command->getFacebookItemId())->where('facebook_node_id', $command->getFacebookAlbumId())->first();

            $photoDescr = new PhotoDescription($facebookItem, $command->getComment());
            $params = [
                'url' => $command->getPhotoUrl(),
                'message' => (string) $photoDescr
            ];

            $response = $this->facebook->postPhotoToGroupAlbum($command->getFacebookAlbumId(), $params, $command->getFacebookToken());
            $response = $response->asArray();

            $facebookItem->facebook_parameters = $params;
            $facebookItem->facebook_post_id = $response['id'];
            $facebookItem->facebook_posted_at = Carbon::now();
            $facebookItem->save();

            // Curious, does facebook not allow comments for items that are _just in time_ ?
            $this->facebook->postCommentToPhoto($response['id'], ['message' => 'Sold'], $command->getFacebookToken());

            // event(new FacebookPhotoPostedToAlbum));
        } catch (Exception $e) {
            //TODO: We can get some of the queued job data from the command itself. So we can tell user when its failed.

            dd($e->getMessage());
            // Identify a place to store max attempts for queue
            if ($command->attempts() == config('queue.max_retries')) {
                $command->delete();
            }
        }
    }
}
