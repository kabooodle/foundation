<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Traits;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Comments\DeleteCommentCommand;
use Kabooodle\Libraries\Linkify\LinkifyableTrait;
use Kabooodle\Models\Comments;
use Kabooodle\Models\Contracts\Commentable;
use Kabooodle\Bus\Commands\Comments\AddCommentCommand;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Kabooodle\Models\User;

/**
 * Class CommentableControllerTrait
 * @package Kabooodle\Http\Controllers\Traits
 */
trait CommentableControllerTrait
{
    use DispatchesJobs, LinkifyableTrait, ValidatesRequests;

    /**
     * @param User                 $actor
     * @param Commentable $commentable
     * @param                      $commentText
     *
     * @return array
     */
    public function handleStoreComment(User $actor, Commentable $commentable, $commentText, $referringUrl = null)
    {
        /** @var Comments $comment */
        $comment = $this->dispatchNow(new AddCommentCommand($actor, $commentable, $commentText, $referringUrl));

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
     * @param User                 $actor
     * @param Commentable $commentable
     * @param Comments             $comment
     *
     * @return array
     */
    public function handleDeleteComment(User $actor, Commentable $commentable, Comments $comment)
    {
        $this->dispatchNow(new DeleteCommentCommand($actor, $commentable, $comment));

        // Gott refresh this relationship.
        $commentable->load('comments');

        return [
            'json' => json_encode(['deleted' => true]),
            'comments' => $commentable->comments->toJson(),
            'total' => $commentable->comments->count()
        ];
    }
}
