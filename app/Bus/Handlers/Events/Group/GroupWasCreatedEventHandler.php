<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Group;

use Kabooodle\Libraries\Emails\KitEmail;
use Kabooodle\Bus\Events\Group\GroupWasCreatedEvent;

/**
 * Class GroupWasCreatedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Group
 */
class GroupWasCreatedEventHandler
{
    /**
     * @param GroupWasCreatedEvent $event
     */
    public function handle(GroupWasCreatedEvent $event)
    {
        $group = $event->getGroup();

        $mail = new KitEmail;
        $mail->setView('groups.emails.created')
            ->setParameters(['group' => $group])
            ->setCallable(function ($m) use ($group) {
                $m->to(user()->email)->subject('Group created on '.env('APP_NAME'));
            })
            ->send();
    }
}