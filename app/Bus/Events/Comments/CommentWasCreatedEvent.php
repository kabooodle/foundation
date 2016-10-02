<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Comments;

use Kabooodle\Models\Comments;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Models\Contracts\CommentableInterface;

/**
 * Class CommentWasCreatedEvent
 * @package Kabooodle\Bus\Events\Comments
 */
final class CommentWasCreatedEvent
{
    use SerializesModels;

    /**
     * @var CommentableInterface
     */
    public $commentable;

    /**
     * @var Comments
     */
    public $comment;

    /**
     * CommentWasCreatedEvent constructor.
     *
     * @param Comments             $comment
     * @param CommentableInterface $commentable
     */
    public function __construct(Comments $comment, CommentableInterface $commentable)
    {
        $this->comment = $comment;
        $this->commentable = $commentable;
    }

    /**
     * @return Comments
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return CommentableInterface
     */
    public function getCommentable()
    {
        return $this->commentable;
    }
}