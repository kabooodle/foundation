<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Comments;

use Kabooodle\Models\User;
use Kabooodle\Models\Contracts\CommentableInterface;

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
     * @var CommentableInterface
     */
    public $commentable;

    /**
     * @var string
     */
    public $comment;

    /**
     * AddCommentCommand constructor.
     *
     * @param User                 $actor
     * @param CommentableInterface $commentable
     * @param string               $comment
     */
    public function __construct(User $actor, CommentableInterface $commentable, $comment)
    {
        $this->actor = $actor;
        $this->commentable = $commentable;
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
     * @return CommentableInterface
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
}
