<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Social\Facebook;

use Kabooodle\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class PostPhotoToGroupAlbumCommand
 * @package Kabooodle\Bus\Commands\Social\Facebook
 */
class PostPhotoToGroupAlbumCommand implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var User
     */
    protected $actor;

    /**
     * @var
     */
    protected $facebookToken;

    /**
     * @var
     */
    protected $facebookAlbumId;

    protected $comment;

    protected $photoUrl;

    protected $facebookItemId;

    /**
     * PostPhotoToGroupAlbumCommand constructor.
     *
     * @param User   $actor
     * @param        $facebookToken
     * @param        $facebookItemId
     * @param        $facebookAlbumId
     * @param        $photoUrl
     * @param string $comment
     */
    public function __construct(User $actor, $facebookToken, $facebookItemId, $facebookAlbumId, $photoUrl, $comment = '')
    {
        $this->actor = $actor;
        $this->facebookToken = $facebookToken;
        $this->facebookItemId = $facebookItemId;
        $this->facebookAlbumId = $facebookAlbumId;
        $this->photoUrl = $photoUrl;
        $this->comment = $comment;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return mixed
     */
    public function getFacebookAlbumId()
    {
        return $this->facebookAlbumId;
    }

    /**
     * @return mixed
     */
    public function getFacebookToken()
    {
        return $this->facebookToken;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    /**
     * @return mixed
     */
    public function getFacebookItemId()
    {
        return $this->facebookItemId;
    }
}
