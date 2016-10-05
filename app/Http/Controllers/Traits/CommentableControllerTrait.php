<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Traits;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Comments\DeleteCommentCommand;
use Kabooodle\Libraries\Linkify\LinkifyableTrait;
use Kabooodle\Models\Comments;
use Kabooodle\Models\Contracts\CommentableInterface;
use Kabooodle\Bus\Commands\Comments\AddCommentCommand;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class CommentableControllerTrait
 * @package Kabooodle\Http\Controllers\Traits
 */
trait CommentableControllerTrait
{
    use DispatchesJobs, LinkifyableTrait, ValidatesRequests;

    /**
     * @param CommentableInterface $commentable
     *
     * @return array
     */
    public function handleStoreComment(CommentableInterface $commentable, $commentText)
    {
        /** @var Comments $comment */
        $comment = $this->dispatchNow(new AddCommentCommand(user(), $commentable, $commentText));

        // Gott refresh this relationship.
        $commentable->load('comments');

        return [
            'json' => $comment->load('author')->toJson(),
            'comments' => $commentable->comments->toJson(),
            'total' => $commentable->comments->count(),
            'html' => $comment->present()->buildComment()
        ];
    }

    /**
     * @param CommentableInterface $commentable
     * @param Comments             $comment
     *
     * @return array
     */
    public function handleDeleteComment(CommentableInterface $commentable, Comments $comment)
    {
        $this->dispatchNow(new DeleteCommentCommand(user(), $commentable, $comment));

        // Gott refresh this relationship.
        $commentable->load('comments');

        return [
            'json' => json_encode(['deleted' => true]),
            'comments' => $commentable->comments->toJson(),
            'total' => $commentable->comments->count()
        ];
    }
}