<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Comments;

use Exception;
use Kabooodle\Bus\Events\Comments\CommentWasCreatedEvent;
use Kabooodle\Bus\Events\Comments\CommentWasDeletedEvent;
use Kabooodle\Models\User;
use Kabooodle\Models\Comments;
use Kabooodle\Models\Contracts\Commentable;
use Kabooodle\Bus\Commands\Comments\DeleteCommentCommand;

/**
 * Class DeleteCommentCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Comments
 */
class DeleteCommentCommandHandler
{
    /**
     * @param DeleteCommentCommand $command
     *
     * @throws Exception
     */
    public function handle(DeleteCommentCommand $command)
    {
        $actor = $command->getActor();
        $commentable = $command->getCommentable();
        $comment = $command->getComment();

        if ($this->userCanDeleteComment($actor, $commentable, $comment)) {
            $comment->delete();

            event(new CommentWasDeletedEvent($actor, $comment));
        } else {
            throw new Exception('User is not authorized to delete the comment');
        }
    }

    /**
     * @param User $actor
     * @param Commentable $commentable
     * @param Comments $comment
     *
     * @return bool
     */
    public function userCanDeleteComment(User $actor, Commentable $commentable, Comments $comment)
    {
        // Is actor author of comment?
        if ($actor->id === $comment->author->id) {
            return true;
        }

        if ($comment->author->id === $commentable->getOwner()->id) {
            return true;
        }

        // Is actor owner of commentable item
        if ($actor->id === $commentable->getOwner()->id) {
            return true;
        }

        return false;
    }
}
