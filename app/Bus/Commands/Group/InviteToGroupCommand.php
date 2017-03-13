<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Group;

use Kabooodle\Models\User;
use Kabooodle\Models\Groups;

/**
 * Class InviteToGroupCommand
 * @package Kabooodle\Bus\Commands\Group
 */
class InviteToGroupCommand
{
    private $email;
    private $group;
    private $user;

    /**
     * InviteToGroupCommand constructor.
     *
     * @param Groups $group
     * @param User   $invitedBy
     * @param        $email
     */
    public function __construct(Groups $group, User $invitedBy, $email)
    {
        $this->group = $group;
        $this->user = $invitedBy;
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Groups
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
