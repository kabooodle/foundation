<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Comments;

use Kabooodle\Models\User;
use Kabooodle\Models\Comments;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Libraries\Emails\KitEmail;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Models\NotificationNotices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Models\Contracts\CommentableInterface;
use Kabooodle\Bus\Events\Comments\CommentWasCreatedEvent;

/**
 * Class CommentWasCreatedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Comments
 */
class CommentWasCreatedEventHandler implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

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

        if ($commentableOwner->checkIsNotifyable('inventory_commented', 'email')) {
            if ($commentableOwner->primaryEmail->isVerified()) {
                $this->notifyOwner($commentableOwner, $comment, $commentable);
            }
        }

        $this->toDatabase($commentableOwner, $comment, $commentable);
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

    /**
     * @param $commentableOwner
     * @param Comments $comment
     * @param CommentableInterface $commentable
     */
    public function toDatabase($commentableOwner, Comments $comment, CommentableInterface $commentable)
    {
        $title = $comment->author->username.' commented on '.$commentable->getName();

        $notification = new NotificationNotices;
        $notification->user_id = $commentableOwner->id;
        $notification->notification_id = null;
        $notification->reference_id = $commentable->id;
        $notification->reference_type = get_class($commentable);
        $notification->payload = '';
        $notification->title = $title;
        $notification->description = '';
        $notification->save();
    }
}
