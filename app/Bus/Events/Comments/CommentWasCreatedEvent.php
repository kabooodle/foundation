<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Comments;

use Kabooodle\Models\Comments;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Models\Contracts\Commentable;

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
     * @param Comments    $comment
     * @param Commentable $commentable
     */
    public function __construct(Comments $comment, Commentable $commentable)
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
