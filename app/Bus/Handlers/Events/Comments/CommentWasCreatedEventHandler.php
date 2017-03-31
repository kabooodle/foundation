<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Comments;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Kabooodle\Models\Comments;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Libraries\Emails\KitEmail;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Models\NotificationNotices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Models\Contracts\Commentable;
use Kabooodle\Services\Comments\CommentsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Bus\Events\Comments\CommentWasCreatedEvent;

/**
 * Class CommentWasCreatedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Comments
 */
class CommentWasCreatedEventHandler implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var CommentsService
     */
    public $commentsService;

    /**
     * @param CommentsService $commentsService
     */
    public function __construct(CommentsService $commentsService)
    {
        $this->commentsService = $commentsService;
    }

    /**
     * @param CommentWasCreatedEvent $event
     *
     * @return bool
     * @throws Exception
     */
    public function handle(CommentWasCreatedEvent $event)
    {
        try {
            /** @var Comments $comment */
            $comment = $event->getComment();

            /** @var Commentable $commentable */
            $commentable = $event->getCommentable();

            /** @var User $commentableOwner */
            $commentableOwner = $commentable->getOwner();

            $this->toOwner($commentableOwner, $comment, $commentable);

            /** @var User|Collection $usersMentioned */
            $usersMentioned = $this->checkCommentHasMentions($comment);
            if ($usersMentioned && $usersMentioned->count() > 0) {
                foreach ($usersMentioned as $userMentioned) {
                    $this->toMentioned($userMentioned, $commentableOwner, $comment, $commentable);
                }
            }
        } catch (ModelNotFoundException $e) {
            $this->job->delete();

            return true;
        } catch (Exception $e) {
            Bugsnag::notifyException($e);

            throw $e;
        }
    }

    /**
     * @param User        $commentableOwner
     * @param Comments    $comment
     * @param Commentable $commentable
     */
    public function toOwner(User $commentableOwner, Comments $comment, Commentable $commentable)
    {
        if ($commentableOwner->checkIsNotifyable('commented_on_item', 'email')) {
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
        $title = $comment->author->username.' mentioned you in a comment on '.$commentable->getName();

        if ($user->checkIsNotifyable('comment_mentioned', 'email')) {
            if ($user->primaryEmail->isVerified()) {
                $mailer = new KitEmail;
                $email = $user->email;

                $mailer->setView('comments.emails.mentioned_in_comment')
                    ->setParameters(['comment' => $comment, 'commentable' => $commentable])
                    ->setCallable(function ($mail) use ($email, $title) {
                        $mail->to($email)
                            ->subject($title);
                    })
                    ->send();
            }
        }

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
        $mentions = $this->commentsService->getMentionsFromComment($string);
        if ($mentions && count($mentions) > 0) {
            return $this->commentsService->getUsernamesFromMentions($mentions[1]);
        }

        return [];
    }
}
