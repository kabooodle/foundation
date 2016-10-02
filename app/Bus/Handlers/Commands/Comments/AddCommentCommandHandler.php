<?php

namespace Kabooodle\Bus\Handlers\Commands\Comments;

use DB;
use Kabooodle\Models\Comments;
use Kabooodle\Bus\Commands\Comments\AddCommentCommand;
use Kabooodle\Bus\Events\Comments\CommentWasCreatedEvent;

/**
 * Class AddCommentCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Comments
 */
class AddCommentCommandHandler
{
    /**
     * @param AddCommentCommand $command
     *
     * @return mixed
     */
    public function handle(AddCommentCommand $command)
    {
        return DB::transaction(function() use ($command) {

            $commentable = $command->getCommentable();

            $comment = new Comments;
            $comment->commentable_id = $commentable->id;
            $comment->commentable_type = get_class($commentable);
            $comment->text_raw = $command->getComment();
            $comment->user_id = $command->getActor()->id;

            $comment->save();

            event(new CommentWasCreatedEvent($comment, $commentable));

            return $comment;
        });
    }
}