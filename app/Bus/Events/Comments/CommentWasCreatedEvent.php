<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Comments;

use Illuminate\Queue\SerializesModels;

/**
 * Class CommentWasCreatedEvent
 * @package Kabooodle\Bus\Events\Comments
 */
final class CommentWasCreatedEvent
{
    use SerializesModels;

    /**
     * @var \Kabooodle\Models\Contracts\Commentable
     */
    public $commentable;

    /**
     * @var \Kabooodle\Models\Comments
     */
    public $comment;

    /**
     * CommentWasCreatedEvent constructor.
     *
     * @param \Kabooodle\Models\Comments                       $comment
     * @param \Kabooodle\Models\Contracts\Commentable $commentable
     */
    public function __construct(\Kabooodle\Models\Comments $comment, \Kabooodle\Models\Contracts\Commentable $commentable)
    {
        $this->comment = $comment;
        $this->commentable = $commentable;
    }

    /**
     * @return \Kabooodle\Models\Comments
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return \Kabooodle\Models\Contracts\Commentable
     */
    public function getCommentable()
    {
        return $this->commentable;
    }
}
