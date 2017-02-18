<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\InventoryGroupings;

use Kabooodle\Models\User;

/**
 * Class CreateInventoryGroupingsCommand
 * @package Kabooodle\Bus\Commands\InventoryGroupings
 */
final class CreateInventoryGroupingsCommand
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $groupings;

    /**
     * CreateInventoryGroupingCommand constructor.
     *
     * @param User $user
     * @param array $groupings
     */
    public function __construct(User $user, array $groupings)
    {
        $this->user = $user;
        $this->groupings = $groupings;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getGroupings(): array
    {
        return $this->groupings;
    }
}