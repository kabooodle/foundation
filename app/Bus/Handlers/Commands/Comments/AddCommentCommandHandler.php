<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Comments;

use DB;
use Kabooodle\Models\Comments;
use Kabooodle\Bus\Commands\Comments\AddCommentCommand;
use Kabooodle\Bus\Events\Comments\CommentWasCreatedEvent;
use Kabooodle\Services\Comments\CommentsService;

/**
 * Class AddCommentCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Comments
 */
class AddCommentCommandHandler
{
    /**
     * @var CommentsService
     */
    protected $commentsService;

    /**
     * @param CommentsService $commentsService
     */
    public function __construct(CommentsService $commentsService)
    {
        $this->commentsService = $commentsService;
    }

    /**
     * @param AddCommentCommand $command
     *
     * @return mixed
     */
    public function handle(AddCommentCommand $command)
    {
        return DB::transaction(function () use ($command) {
            $commentable = $command->getCommentable();

            $comment = new Comments;
            $comment->commentable_id = $commentable->id;
            $comment->commentable_type = get_class($commentable);
            $comment->text_raw = $this->commentsService->highlightUsernames($command->getComment());
            $comment->user_id = $command->getActor()->id;
            $comment->reference_url = $command->getReferringUrl();

            $comment->save();

            event(new CommentWasCreatedEvent($comment, $commentable));

            return $comment;
        });
    }
}
