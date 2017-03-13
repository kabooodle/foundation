<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Group;

use Kabooodle\Models\User;

/**
 * Class AddGroupCommand
 * @package Kabooodle\Bus\Commands\Group
 */
final class AddGroupCommand
{
    /**
     * AddGroupCommand constructor.
     *
     * @param       $name
     * @param array $memberEmails
     * @param       $privacy
     * @param User  $createdBy
     */
    public function __construct($name, $memberEmails = [], $privacy, User $createdBy)
    {
        $this->name = $name;
        $this->memberEmails = $memberEmails;
        $this->user = $createdBy;
        $this->privacy = $privacy;
    }

    /**
     * @return array
     */
    public function getMemberEmails()
    {
        return $this->memberEmails;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }
}
