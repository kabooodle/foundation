<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Comments;

use Kabooodle\Models\User;
use Kabooodle\Models\Comments;
use Kabooodle\Models\Contracts\CommentableInterface;

/**
 * Class DeleteCommentCommand.
 */
final class DeleteCommentCommand
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
     * @var Comments
     */
    public $comment;

    /**
     * DeleteCommentCommand constructor.
     *
     * @param User                 $actor
     * @param CommentableInterface $commentable
     * @param Comments             $comment
     */
    public function __construct(User $actor, CommentableInterface $commentable, Comments $comment)
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
     * @return Comments
     */
    public function getComment()
    {
        return $this->comment;
    }
}
