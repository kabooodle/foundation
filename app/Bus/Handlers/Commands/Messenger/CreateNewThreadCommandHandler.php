<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Messenger;

use DB;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Kabooodle\Bus\Events\Messenger\ThreadWasCreatedEvent;
use Kabooodle\Bus\Commands\Messenger\CreateNewThreadCommand;
use Kabooodle\Foundation\Exceptions\Messenger\CannotMessageYourselfException;

/**
 * Class CreateNewThreadCommandHandler
 */
class CreateNewThreadCommandHandler
{
    /**
     * @param CreateNewThreadCommand $command
     *
     * @return mixed
     */
    public function handle(CreateNewThreadCommand $command)
    {
        return DB::transaction(function() use ($command) {

            $sender = $command->getSender();
            $recipients = $command->getRecipientIds();
            $subject = $command->getSubject();
            $message = $command->getMessage();

            foreach ($recipients as $recipient) {
                if ((int) $recipient == (int) user()->id) {
                    throw new CannotMessageYourselfException;
                }
            }

            $thread = Thread::create([
                'subject' => $subject
            ]);

            Message::create([
                'thread_id' => $thread->id,
                'user_id' => $sender->id,
                'body' => $message
            ]);

            Participant::create([
                'thread_id' => $thread->id,
                'user_id' => $sender->id,
                'last_read' => Carbon::now()
            ]);

            $thread->addParticipant($recipients);

            event(new ThreadWasCreatedEvent($thread, $sender));

            return $thread;
        });
    }
}