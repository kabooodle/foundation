<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Group;

use Kabooodle\Models\Groups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Group\AddGroupCommand;
use Kabooodle\Bus\Events\Group\GroupWasCreatedEvent;
use Kabooodle\Bus\Commands\Group\InviteToGroupCommand;

/**
 * Class AddGroupCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Group
 */
class AddGroupCommandHandler
{
    use DispatchesJobs;

    /**
     * @param AddGroupCommand $command
     *
     * @return Model
     */
    public function handle(AddGroupCommand $command)
    {
        $group = Groups::factory([
            'name' => $command->getName(),
            'privacy' => $command->getPrivacy()
        ]);

        $group->admins()->save($command->getUser());

        // If there are emails, lets let the appropriate command handler do the work
        // because we need to perform logic that checks each email address
        // against existing accounts, etc;
        if ($command->getMemberEmails()) {
            foreach ($command->getMemberEmails() as $email) {
                $this->dispatch(new InviteToGroupCommand($group, $command->getUser(), $email));
            }
        }

        event(new GroupWasCreatedEvent($group));

        return $group;
    }
}
