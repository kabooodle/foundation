<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Group;

use Kabooodle\Models\Groups;

/**
 * Class GroupWasCreatedEvent
 * @package Kabooodle\Bus\Events\Group
 */
class GroupWasCreatedEvent
{
    /**
     * GroupWasCreatedEvent constructor.
     *
     * @param Groups $group
     */
    public function __construct(Groups $group)
    {
        $this->group = $group;
    }

    /**
     * @return Groups
     */
    public function getGroup()
    {
        return $this->group;
    }
}
