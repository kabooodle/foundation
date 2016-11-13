<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Comments;

use Kabooodle\Models\User;
use Kabooodle\Models\Comments;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Libraries\Emails\KitEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Models\Contracts\CommentableInterface;
use Kabooodle\Bus\Events\Comments\CommentWasCreatedEvent;

/**
 * Class CommentWasCreatedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Comments
 */
class CommentWasCreatedEventHandler implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @param CommentWasCreatedEvent $event
     */
    public function handle(CommentWasCreatedEvent $event)
    {
        /** @var Comments $comment */
        $comment = $event->getComment();

        /** @var CommentableInterface $commentable */
        $commentable = $event->getCommentable();

        /** @var User $commentableOwner */
        $commentableOwner = $commentable->getOwner();

        if ($commentableOwner->checkIsNotifyable('inventory_commented', 'web')) {
            $mailOwner = $this->notifyOwner($commentableOwner, $comment, $commentable);
        }
    }

    /**
     * @param User $commentableOwner
     * @param      $comment
     * @param      $commentable
     *
     * @return mixed
     */
    public function notifyOwner($commentableOwner, Comments $comment, CommentableInterface $commentable)
    {
        $mailer = new KitEmail;

        return $mailer->setView('comments.emails.newcomment_onowner')
            ->setParameters(['comment' => $comment, 'commentable' => $commentable])
            ->setCallable(function ($mail) use ($commentableOwner) {
                $mail->to($commentableOwner->email)
                    ->subject('New comment');
            })
            ->send();
    }
}
