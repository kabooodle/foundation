<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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

            $randomAvatar = 'resources/monster'.rand(1,9).'.jpg';
            $avatarPath = 'https://'.env('AWS_BUCKET').'.s3.amazonaws.com/'.$randomAvatar;

            $user = $event->getUser();
            $file = new Files;
            $file->fileable_id = $user->id;
            $file->fileable_type = get_class($user);
            $file->bucket_name = env('AWS_BUCKET');
            $file->location = $avatarPath;
            $file->key = $randomAvatar;
            $file->save();

            $user->avatar()->save($file);
        });
    }
}