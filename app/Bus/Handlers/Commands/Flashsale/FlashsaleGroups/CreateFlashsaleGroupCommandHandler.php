<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale\FlashsaleGroups;

use DB;
use Kabooodle\Models\User;
use Kabooodle\Models\FlashsaleGroups;
use Kabooodle\Bus\Commands\Flashsale\FlashsaleGroups\CreateFlashsaleGroupCommand;

/**
 * Class CreateFlashsaleGroupCommandHandler
 */
class CreateFlashsaleGroupCommandHandler
{
    /**
     * @param CreateFlashsaleGroupCommand $command
     *
     * @return mixed
     */
    public function handle(CreateFlashsaleGroupCommand $command)
    {
        return DB::transaction(function() use ($command) {
            $actor = $command->getActor();
            $groupName = $command->getName();
            $userIds = $command->getUserIds();

            $group = FlashsaleGroups::create([
                'name' => $groupName,
                'owner_id' => $actor->id
            ]);

            $actualExistingUsers = User::whereIn('id', $userIds)->get();

            $group->users()->sync($actualExistingUsers);

            $group->save();

            return $group;
        });
    }
}
