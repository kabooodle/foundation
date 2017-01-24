<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use DB;
use Kabooodle\Models\Files;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;

/**
 * Class AssignUserGenericAvatar
 */
class AssignUserGenericAvatar
{
    /**
     * @param UserWasCreatedEvent $event
     *
     * @return mixed
     */
    public function handle(UserWasCreatedEvent $event)
    {
        return DB::transaction(function() use ($event) {
            $user = $event->getUser();
            $file = new Files;
            $file->fileable_id = $user->id;
            $file->fileable_type = get_class($user);
            $file->bucket_name = env('AWS_BUCKET');
            $file->location = defaultAvatar();
            $file->key = 'resources/roboto-avatar.png';
            $file->save();

            $user->avatar()->save($file);
        });
    }
}