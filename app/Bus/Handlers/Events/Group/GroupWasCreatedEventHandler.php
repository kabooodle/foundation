<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Group;

use Illuminate\Contracts\Mail\Mailer;
use Kabooodle\Bus\Events\Group\GroupWasCreatedEvent;

/**
 * Class GroupWasCreatedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Group
 */
class GroupWasCreatedEventHandler
{
    /**
     * GroupWasCreatedEventHandler constructor.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param GroupWasCreatedEvent $event
     */
    public function handle(GroupWasCreatedEvent $event)
    {
        $group = $event->getGroup();
        $this->mailer->send('groups.emails.created', ['group' => $group], function ($m) use ($group) {
            $m->to(user()->email)->subject('Group created on '.env('APP_NAME'));
        });
    }
}