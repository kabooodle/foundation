<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Comments;

use Kabooodle\Models\User;
use Kabooodle\Models\Comments;
use Kabooodle\Models\Contracts\Commentable;

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
     * @var Commentable
     */
    public $commentable;

    /**
     * @var Comments
     */
    public $comment;

    /**
     * DeleteCommentCommand constructor.
     *
     * @param User $actor
     * @param Commentable $commentable
     * @param Comments $comment
     */
    public function __construct(User $actor, Commentable $commentable, Comments $comment)
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
     * @return Commentable
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
