<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Messenger;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Kabooodle\Models\Threads;
use Kabooodle\Models\ThreadMessages;
use Kabooodle\Models\ThreadParticipants;
use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Bus\Events\Messenger\ThreadWasCreatedEvent;

/**
 * Class NotifyNewThreadWasCreatedEvent
 */
class NotifyNewThreadWasCreatedEvent
{
    /**
     * @param ThreadWasCreatedEvent $event
     */
    public function handle(ThreadWasCreatedEvent $event)
    {
        $thread = $event->getThread();
        $sender = $event->getSender();
        $message = $event->getMessage();
        $thread->fresh();
        $participants = $thread->participants;

        try {
            /** @var ThreadParticipants $participant */
            foreach ($participants as $participant) {

                // Don't notify yourself silly pants.
                if ($sender->id == $participant->user->id) {
                    continue;
                }

                if ($participant->user->primaryEmail && $participant->user->primaryEmail->isVerified() && $participant->user->checkIsNotifyable('thread_created', 'email')) {
                    $this->toEmail($sender, $participant->user, $thread, $message);
                }
            }
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    public function toWeb()
    {

    }

    /**
     * @param User           $sender
     * @param User           $recipient
     * @param Threads        $thread
     * @param ThreadMessages $message
     */
    public function toEmail(User $sender, User $recipient, Threads $thread, ThreadMessages $message)
    {
        $email = new PiperEmail;
        $email->setView('messenger.emails.newthread')
            ->setCallable(function($m) use ($recipient, $sender, $thread) {
                $m->to($recipient->primaryEmail->address)
                    ->subject($sender->username.' send you a new message : '.$thread->subject);
            })
            ->setParameters([
                'message' => $message
            ])
            ->send();
    }
}
