<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Messenger;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Kabooodle\Models\Threads;
use Illuminate\Bus\Queueable;
use Cmgmyr\Messenger\Models\Thread;
use Kabooodle\Models\ThreadMessages;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Models\ThreadParticipants;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Libraries\Emails\PiperEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Libraries\WebSockets\WebSocket;
use Kabooodle\Bus\Events\Messenger\MessageWasAddedToThreadEvent;

/**
 * Class NotifyNewMessageForThreadCreated
 */
class NotifyNewMessageForThreadCreated implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param MessageWasAddedToThreadEvent $event
     */
    public function handle(MessageWasAddedToThreadEvent $event)
    {
        $thread = $event->getThread();
        $message = $event->getMessage();
        $sender = $event->getAuthor();
        $participants = $thread->participants;

        try {
            $this->toWeb($thread, $message);

            /** @var ThreadParticipants $participant */
            foreach ($participants as $participant) {
                if ($participant->primaryEmail && $participant->primaryEmail->isValid() && $participant->checkIsNotifyable('thread_message_added', 'email')) {
                    $this->toEmail($sender, $participant->user, $thread, $message);
                }
            }
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param User           $sender
     * @param User           $recipient
     * @param Thread         $thread
     * @param ThreadMessages $message
     */
    public function toEmail(User $sender, User $recipient, Thread $thread, ThreadMessages $message)
    {
        $email = new PiperEmail;
        $email->setView('messenger.emails.newmessage')
            ->setCallable(function($m) use ($recipient, $sender) {
                $m->to($recipient->primaryEmail->address)
                    ->subject($sender->full_name.' added a new response to a message.');
            })
            ->setParameters([
                'message' => $message
            ])
            ->send();
    }

    /**
     * @param Threads        $thread
     * @param ThreadMessages $message
     */
    public function toWeb(Threads $thread, ThreadMessages $message)
    {
        $pusher = new WebSocket;
        $pusher->setChannelName('presence.'.env('APP_ENV').'.threads.'.$thread->id)
            ->setEventName('response:added')
            ->setPayload([
                'message' => $message
            ])
            ->send();
    }
}
