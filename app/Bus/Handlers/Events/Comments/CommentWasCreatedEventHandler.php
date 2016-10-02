<?php

namespace Kabooodle\Bus\Handlers\Events\Comments;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
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

    }
}