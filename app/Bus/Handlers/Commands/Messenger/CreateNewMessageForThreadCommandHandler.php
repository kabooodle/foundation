<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Messenger;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\ThreadMessages;
use Kabooodle\Models\ThreadParticipants;
use Kabooodle\Bus\Events\Messenger\MessageWasAddedToThreadEvent;
use Kabooodle\Bus\Commands\Messenger\CreateNewMessageForThreadCommand;

/**
 * Class CreateNewMessageForThreadCommandHandler
 */
class CreateNewMessageForThreadCommandHandler
{
    /**
     * @param CreateNewMessageForThreadCommand $command
     *
     * @return mixed
     */
    public function handle(CreateNewMessageForThreadCommand $command)
    {
        return DB::transaction(function() use ($command) {

            $thread = $command->getThread();
            $author = $command->getAuthor();
            $message = $command->getMessage();

            $timestamp = Carbon::now();

            $thread->updated_at = $timestamp;
            $thread->save();

            $message = ThreadMessages::create([
                'thread_id' => $thread->id,
                'user_id'   => $author->id,
                'body'      => $message,
            ]);

            $participant = ThreadParticipants::firstOrCreate([
                'thread_id' => $thread->id,
                'user_id'   => $author->id
            ]);
            $participant->last_read = $timestamp;
            $participant->save();

            event(new MessageWasAddedToThreadEvent($thread, $message, $author));

            return $message;
        });
    }
}
