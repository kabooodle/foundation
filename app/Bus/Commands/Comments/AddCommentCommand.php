<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Comments;

use Kabooodle\Models\User;
use Kabooodle\Models\Contracts\Commentable;

/**
 * Class AddCommentCommand.
 */
final class AddCommentCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var Commentable
     */
    public $commentable;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var string
     */
    public $referringUrl;

    /**
     * @param User        $actor
     * @param Commentable $commentable
     * @param string      $comment
     * @param string|null $referringUrl
     */
    public function __construct(User $actor, Commentable $commentable, string $comment, string $referringUrl = null)
    {
        $this->actor = $actor;
        $this->commentable = $commentable;
        $this->comment = $comment;
        $this->referringUrl = $referringUrl;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return Commentable
     */
    public function getCommentable()
    {
        return $this->commentable;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return null|string
     */
    public function getReferringUrl()
    {
        return $this->referringUrl;
    }
}
