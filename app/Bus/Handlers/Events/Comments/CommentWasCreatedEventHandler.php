<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Comments;

use DB;
use Kabooodle\Models\User;
use Kabooodle\Models\Comments;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Libraries\Emails\KitEmail;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Models\NotificationNotices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Models\Contracts\Commentable;
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
        DB::transaction(function() use ($event) {
            /** @var Comments $comment */
            $comment = $event->getComment();

            /** @var Commentable $commentable */
            $commentable = $event->getCommentable();

            /** @var User $commentableOwner */
            $commentableOwner = $commentable->getOwner();

            $this->toOwner($commentableOwner, $comment, $commentable);

            /** @var User|Collection $usersMentioned */
            $usersMentioned = $this->checkCommentHasMentions($comment);
            if ($usersMentioned && $usersMentioned->count() > 0){
                foreach($usersMentioned as $userMentioned){
                    $this->toMentioned($userMentioned, $commentableOwner, $comment, $commentable);
                }
            }
        });
    }

    /**
     * @param User        $commentableOwner
     * @param Comments    $comment
     * @param Commentable $commentable
     */
    public function toOwner(User $commentableOwner, Comments $comment, Commentable $commentable)
    {
        if ($commentableOwner->checkIsNotifyable('inventory_commented', 'email')) {
            if ($commentableOwner->primaryEmail->isVerified()) {
                $mailer = new KitEmail;

                $mailer->setView('comments.emails.newcomment_onowner')
                    ->setParameters(['comment' => $comment, 'commentable' => $commentable])
                    ->setCallable(function ($mail) use ($commentableOwner) {
                        $mail->to($commentableOwner->email)
                            ->subject('New comment');
                    })
                    ->send();
            }
        }

        $title = $comment->author->username.' commented on '.$commentable->getName();

        $notification = new NotificationNotices;
        $notification->user_id = $commentableOwner->id;
        $notification->notification_id = null;
        $notification->reference_id = $commentable->id;
        $notification->reference_type = get_class($commentable);
        $notification->reference_url = $comment->reference_url;
        $notification->payload = '';
        $notification->title = $title;
        $notification->description = '';
        $notification->save();
    }

    /**
     * @param User        $user
     * @param User        $commentableOwner
     * @param Comments    $comment
     * @param Commentable $commentable
     */
    public function toMentioned(User $user,  User $commentableOwner, Comments $comment, Commentable $commentable)
    {
//        if ($user->checkIsNotifyable('inventory_commented', 'email')) {
//            if ($user->primaryEmail->isVerified()) {
//                $mailer = new KitEmail;
//
//                $mailer->setView('comments.emails.newcomment_onowner')
//                    ->setParameters(['comment' => $comment, 'commentable' => $commentable])
//                    ->setCallable(function ($mail) use ($commentableOwner) {
//                        $mail->to($commentableOwner->email)
//                            ->subject('New comment');
//                    })
//                    ->send();
//            }
//        }

        $title = $comment->author->username.' mentioned you in a comment on '.$commentable->getName();

        $notification = new NotificationNotices;
        $notification->user_id = $user->id;
        $notification->notification_id = null;
        $notification->reference_id = $commentable->id;
        $notification->reference_type = get_class($commentable);
        $notification->payload = '';
        $notification->reference_url = $comment->reference_url;
        $notification->title = $title;
        $notification->description = '';
        $notification->save();
    }

    /**
     * @param string $string
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function checkCommentHasMentions(string $string)
    {
        preg_match_all('/@([\w_\-\.]+\s)/', $string, $mentions);
        if ($mentions && count($mentions) > 0) {
            // [0] holds the matches, [1] holds the matches without the ampersand.
            $usernames = array_map(function($mention){
                return strtolower(trim($mention));
            }, $mentions[1]);

            return User::whereIn('username', $usernames)->get();
        }

        return [];
    }
}
