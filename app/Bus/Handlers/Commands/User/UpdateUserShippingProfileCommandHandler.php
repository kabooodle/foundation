<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Kabooodle\Bus\Commands\User\UpdateUserShippingProfileCommand;

/**
 * Class UpdateUserShippingProfileCommandHandler
 */
class UpdateUserShippingProfileCommandHandler
{
    /**
     * @param UpdateUserShippingProfileCommand $command
     *
     * @return mixed
     */
    public function handle(UpdateUserShippingProfileCommand $command)
    {
        $user = $command->getUser();
        $user->kabooodle_as_shipping = $command->isKabooodleDefaultShippingProvider();
        $user->save();

        return $user;
    }
}
