<?php

namespace Kabooodle\Http\Controllers\Traits;

use Illuminate\Foundation\Bus\DispatchesJobs;
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

        return [
            'json' => $comment->toJson(),
            'total' => $commentable->comments->count(),
            'html' => $comment->present()->buildComment()
        ];
    }

    public function deleteComment()
    {
        //
    }
}